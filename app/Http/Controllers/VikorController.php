<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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
        $totalBobot = $kriterias->sum('bobot');
        $epsilon = 0.01;

        if (!$user->isAdmin() && abs($totalBobot - 1.0) > $epsilon) {
            return view('vikor.hasil', [
                'error' => 'Total bobot kriteria saat ini adalah ' . number_format($totalBobot, 2) . '. Harap sesuaikan bobot kriteria agar totalnya tepat 1.0 sebelum melakukan perhitungan VIKOR.'
            ]);
        }

        $alternatifs = $user->isAdmin()
            ? Alternatif::with('nilaiAlternatifs')->get()
            : $user->alternatifs()->with('nilaiAlternatifs')->get();

        if ($kriterias->isEmpty() || $alternatifs->isEmpty()) {
            return view('vikor.hasil', [
                'error' => 'Data kriteria atau alternatif belum lengkap. Harap lengkapi terlebih dahulu.'
            ]);
        }

        foreach ($alternatifs as $alternatif) {
            foreach ($kriterias as $kriteria) {
                if (!$alternatif->getNilaiByKriteria($kriteria)) {
                    return view('vikor.hasil', [
                        'error' => 'Nilai untuk ' . $alternatif->nama_alternatif . ' pada kriteria ' . $kriteria->nama_kriteria . ' belum diinput. Harap lengkapi nilai semua alternatif untuk semua kriteria.'
                    ]);
                }
            }
        }

        // 1. Hitung F* (Nilai Ideal Positif) dan F-
        $fStar = [];
        $fMinus = [];

        foreach ($kriterias as $kriteria) {
            $nilaiList = $alternatifs->map(fn($alt) => $alt->getNilaiByKriteria($kriteria)->nilai)->filter()->values();

            if ($nilaiList->isEmpty()) {
                return view('vikor.hasil', [
                    'error' => 'Tidak ada nilai yang ditemukan untuk kriteria ' . $kriteria->nama_kriteria . '. Harap periksa input nilai alternatif.'
                ]);
            }

            if ($kriteria->tipe == 'benefit') {
                $fStar[$kriteria->id] = $nilaiList->max();
                $fMinus[$kriteria->id] = $nilaiList->min();
            } else {
                $fStar[$kriteria->id] = $nilaiList->min();
                $fMinus[$kriteria->id] = $nilaiList->max();
            }
        }

        // 2. Hitung Nilai Si (Utility Measure) dan Ri (Regret Measure)
        $Si = [];
        $Ri = [];

        foreach ($alternatifs as $alternatif) {
            $s_val = 0;
            $r_val = 0;

            foreach ($kriterias as $kriteria) {
                $nilai = $alternatif->getNilaiByKriteria($kriteria)->nilai;
                $bobot = $kriteria->bobot;

                $fstar = $fStar[$kriteria->id];
                $fminus = $fMinus[$kriteria->id];

                $denom = ($fstar - $fminus);
                if (abs($denom) < 1e-9) {
                    $normalized = 0;
                } else {
                    if ($kriteria->tipe === 'benefit') {
                        $normalized = ($fstar - $nilai) / $denom;
                    } else {
                        $normalized = ($nilai - $fstar) / abs($fminus - $fstar);
                    }
                }

                $weighted = $bobot * $normalized;
                $s_val += $weighted;
                $r_val = max($r_val, $weighted);
            }

            $Si[$alternatif->id] = $s_val;
            $Ri[$alternatif->id] = $r_val;
        }

        // 3. Hitung Qi (VIKOR Index)
        if (empty($Si) || empty($Ri)) {
            return view('vikor.hasil', [
                'error' => 'Gagal menghitung nilai Si atau Ri. Pastikan semua alternatif memiliki nilai untuk semua kriteria.'
            ]);
        }

        $sMin = min($Si);
        $sMax = max($Si);
        $rMin = min($Ri);
        $rMax = max($Ri);

        $v = 0.5;
        $Qi = [];

        foreach ($alternatifs as $alternatif) {
            $id = $alternatif->id;

            $sRange = ($sMax - $sMin);
            $qi_s = ($sRange == 0) ? 0 : ($Si[$id] - $sMin) / $sRange;

            $rRange = ($rMax - $rMin);
            $qi_r = ($rRange == 0) ? 0 : ($Ri[$id] - $rMin) / $rRange;

            $Qi[$id] = $v * $qi_s + (1 - $v) * $qi_r;
        }

        // 4. Perangkingan
        $ranking = [];
        foreach ($alternatifs as $alt) {
            $id = $alt->id;
            $ranking[] = [
                'id' => $id,
                'alternatif' => $alt->nama_alternatif,
                'Si' => round($Si[$id], 6),
                'Ri' => round($Ri[$id], 6),
                'Qi' => round($Qi[$id], 6),
            ];
        }

        usort($ranking, fn($a, $b) => $a['Qi'] <=> $b['Qi']);

        // 5. Penentuan Solusi Kompromi
        $kandidatTerbaik = $ranking[0] ?? null;
        $statusSolusi = 'Tidak ada alternatif untuk dihitung.';
        $DQ = 0;

        if (count($ranking) > 0) {
            $statusSolusi = 'Tidak dapat menentukan solusi kompromi.';
            $DQ = (count($alternatifs) > 1) ? (1 / (count($alternatifs) - 1)) : 0;

            if (count($ranking) > 1) {
                $A1 = $ranking[0];
                $A2 = $ranking[1];

                $condition1 = (abs($A2['Qi'] - $A1['Qi']) >= $DQ);

                $sSortedKeys = collect($Si)->sort()->keys()->first();
                $rSortedKeys = collect($Ri)->sort()->keys()->first();
                $condition2 = ($A1['id'] == $sSortedKeys || $A1['id'] == $rSortedKeys);

                if ($condition1 && $condition2) {
                    $statusSolusi = 'A1 adalah solusi kompromi terbaik.';
                } elseif (!$condition1) {
                    $statusSolusi = 'Tidak ada solusi kompromi yang jelas.';
                    $setSolusiKompromi = [];
                    foreach ($ranking as $r) {
                        if (abs($r['Qi'] - $A1['Qi']) < $DQ) {
                            $setSolusiKompromi[] = $r['alternatif'];
                        } else {
                             if ($r['Qi'] > $A1['Qi']) {
                                break;
                            }
                        }
                    }
                    $kandidatTerbaik['set_solusi_kompromi'] = array_values(array_unique($setSolusiKompromi));
                } elseif (!$condition2) {
                    $statusSolusi = 'Solusi kompromi tidak stabil.';
                    $kandidatTerbaik['set_solusi_kompromi'] = array_values(array_unique([$A1['alternatif'], $A2['alternatif']]));
                } else {
                    $statusSolusi = 'Solusi kompromi ditemukan (kondisi non-standar).';
                }
            } else {
                $statusSolusi = 'Hanya ada satu alternatif yang tersedia.';
            }
        }

        if ($kandidatTerbaik) {
            $kandidatTerbaik['status'] = $statusSolusi;
        }

        return view('vikor.hasil', compact(
            'kriterias',
            'alternatifs',
            'fStar',
            'fMinus',
            'Si',
            'Ri',
            'Qi',
            'ranking',
            'kandidatTerbaik',
            'DQ'
        ));
    }
}
