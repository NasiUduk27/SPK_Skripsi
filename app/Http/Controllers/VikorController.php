<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Import model User untuk type hinting

class VikorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function hitung()
    {
        /** @var User $user */
        $user = Auth::user();

        $kriterias = Kriteria::all();

        if ($user->isAdmin()) {
            $alternatifs = Alternatif::with('nilaiAlternatifs')->get(); // Admin melihat semua
        } else {
            $alternatifs = $user->alternatifs()->with('nilaiAlternatifs')->get(); // User biasa hanya melihat miliknya
        }

        if ($kriterias->isEmpty() || $alternatifs->isEmpty()) {
            return view('vikor.hasil', [
                'error' => 'Data kriteria atau alternatif belum lengkap. Harap lengkapi terlebih dahulu.'
            ]);
        }

        foreach ($alternatifs as $alternatif) {
            foreach ($kriterias as $kriteria) {
                $nilai = $alternatif->getNilaiByKriteria($kriteria);
                if (!$nilai) {
                    return view('vikor.hasil', [
                        'error' => 'Nilai untuk ' . $alternatif->nama_alternatif . ' pada kriteria ' . $kriteria->nama_kriteria . ' belum diinput. Harap lengkapi nilai semua alternatif untuk semua kriteria.'
                    ]);
                }
            }
        }

        // --- Bagian perhitungan VIKOR (tetap sama seperti sebelumnya) ---
        // 1. Normalisasi Matriks (Fij, F*j, Fj-)
        $fStar = [];
        $fMinus = [];

        foreach ($kriterias as $kriteria) {
            $nilaiKriteria = [];
            foreach ($alternatifs as $alternatif) {
                $nilaiKriteria[] = $alternatif->getNilaiByKriteria($kriteria)->nilai;
            }

            $minVal = min($nilaiKriteria);
            $maxVal = max($nilaiKriteria);

            if ($kriteria->tipe == 'benefit') {
                $fStar[$kriteria->id] = $maxVal;
                $fMinus[$kriteria->id] = $minVal;
            } else { // cost
                $fStar[$kriteria->id] = $minVal;
                $fMinus[$kriteria->id] = $maxVal;
            }
        }

        // 2. Hitung Nilai Si dan Ri
        $Si = [];
        $Ri = [];

        foreach ($alternatifs as $alternatif) {
            $s_val = 0;
            $r_val = 0;
            foreach ($kriterias as $kriteria) {
                $nilai = $alternatif->getNilaiByKriteria($kriteria)->nilai;
                $f_star = $fStar[$kriteria->id];
                $f_minus = $fMinus[$kriteria->id];
                $bobot = $kriteria->bobot;

                $denominator = ($f_star - $f_minus);
                if (abs($denominator) < 1e-9) {
                    $norm_nilai = 0;
                } else {
                    if ($kriteria->tipe == 'benefit') {
                        $norm_nilai = ($f_star - $nilai) / $denominator;
                    } else {
                        $norm_nilai = ($nilai - $f_minus) / $denominator;
                    }
                }

                $s_val += $bobot * $norm_nilai;
                $r_val = max($r_val, $bobot * $norm_nilai);
            }
            $Si[$alternatif->id] = $s_val;
            $Ri[$alternatif->id] = $r_val;
        }

        // 3. Hitung Qj
        $sMin = (empty($Si)) ? 0 : min($Si);
        $sMax = (empty($Si)) ? 0 : max($Si);
        $rMin = (empty($Ri)) ? 0 : min($Ri);
        $rMax = (empty($Ri)) ? 0 : max($Ri);

        $v = 0.5;

        $Qi = [];
        foreach ($alternatifs as $alternatif) {
            $qi_s = 0;
            if (($sMax - $sMin) != 0) {
                $qi_s = ($Si[$alternatif->id] - $sMin) / ($sMax - $sMin);
            }

            $qi_r = 0;
            if (($rMax - $rMin) != 0) {
                $qi_r = ($Ri[$alternatif->id] - $rMin) / ($rMax - $rMin);
            }

            $Qi[$alternatif->id] = $v * $qi_s + (1 - $v) * $qi_r;
        }

        // 4. Perangkingan
        $ranking = [];
        foreach ($alternatifs as $alternatif) {
            $ranking[] = [
                'alternatif' => $alternatif->nama_alternatif,
                'Si' => $Si[$alternatif->id],
                'Ri' => $Ri[$alternatif->id],
                'Qi' => $Qi[$alternatif->id],
                'id' => $alternatif->id,
            ];
        }

        usort($ranking, function ($a, $b) {
            $tolerance = 1e-9;
            if (abs($a['Qi'] - $b['Qi']) < $tolerance) {
                return 0;
            }
            return $a['Qi'] <=> $b['Qi'];
        });

        $kandidatTerbaik = null;
        $statusSolusi = 'Tidak dapat menentukan solusi kompromi.';

        if (count($ranking) > 0) {
            $kandidatTerbaik = $ranking[0];

            $m = count($alternatifs);
            $DQ = ($m > 1) ? (1 / ($m - 1)) : 0;

            if (count($ranking) > 1) {
                $A1 = $ranking[0];
                $A2 = $ranking[1];

                $condition1 = (abs($A2['Qi'] - $A1['Qi']) >= $DQ);

                $sSortedKeys = collect($Si)->sort()->keys()->toArray();
                $rSortedKeys = collect($Ri)->sort()->keys()->toArray();

                $A1_id_from_db = $A1['id'];

                $bestS_id = count($sSortedKeys) > 0 ? $sSortedKeys[0] : null;
                $bestR_id = count($rSortedKeys) > 0 ? $rSortedKeys[0] : null;

                $condition2 = ($bestS_id == $A1_id_from_db || $bestR_id == $A1_id_from_db);

                if ($condition1 && $condition2) {
                    $statusSolusi = 'A1 adalah solusi kompromi terbaik.';
                } elseif (!$condition1) {
                    $statusSolusi = 'Tidak ada solusi kompromi yang jelas. Alternatif ' . $A1['alternatif'] . ' dan ' . $A2['alternatif'] . ' (dan mungkin lainnya) adalah set solusi kompromi.';
                    $setSolusiKompromi = [$A1['alternatif']];
                    for ($i = 1; $i < count($ranking); $i++) {
                        if (abs($ranking[$i]['Qi'] - $A1['Qi']) < $DQ) {
                            $setSolusiKompromi[] = $ranking[$i]['alternatif'];
                        } else {
                            break;
                        }
                    }
                    $kandidatTerbaik['set_solusi_kompromi'] = $setSolusiKompromi;
                } elseif (!$condition2) {
                    $statusSolusi = 'Tidak ada solusi kompromi yang stabil. Pilihan terbaik adalah set solusi kompromi { ' . $A1['alternatif'] . ', ' . $A2['alternatif'] . ' }.';
                    $kandidatTerbaik['set_solusi_kompromi'] = [$A1['alternatif'], $A2['alternatif']];
                } else {
                    $statusSolusi = 'Solusi kompromi ditemukan.';
                }
            } else {
                $statusSolusi = 'Hanya ada satu alternatif yang tersedia.';
            }
        } else {
            $statusSolusi = 'Tidak ada alternatif untuk dihitung.';
        }

        $kandidatTerbaik['status'] = $statusSolusi;

        return view('vikor.hasil', compact('kriterias', 'alternatifs', 'fStar', 'fMinus', 'Si', 'Ri', 'Qi', 'ranking', 'kandidatTerbaik', 'DQ'));
    }
}
