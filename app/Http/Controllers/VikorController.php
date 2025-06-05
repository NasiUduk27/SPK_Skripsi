<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use Illuminate\Http\Request;

class VikorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function hitung()
    {
        $kriterias = Kriteria::all();
        $alternatifs = Alternatif::with('nilaiAlternatifs')->get();

        if ($kriterias->isEmpty() || $alternatifs->isEmpty() || $alternatifs->first()->nilaiAlternatifs->isEmpty()) {
            return view('vikor.hasil', [
                'error' => 'Data kriteria, alternatif, atau nilai alternatif belum lengkap. Harap lengkapi terlebih dahulu.'
            ]);
        }

        // 1. Normalisasi Matriks (Fij, F*j, Fj-)
        $matriksNormalisasi = [];
        $fStar = []; // Nilai F*j (maksimum untuk Benefit, minimum untuk Cost)
        $fMinus = []; // Nilai Fj- (minimum untuk Benefit, maksimum untuk Cost)

        foreach ($kriterias as $kriteria) {
            $nilaiKriteria = [];
            foreach ($alternatifs as $alternatif) {
                $nilai = $alternatif->getNilaiByKriteria($kriteria);
                if ($nilai) {
                    $nilaiKriteria[] = $nilai->nilai;
                } else {
                    return view('vikor.hasil', [
                        'error' => 'Nilai untuk ' . $alternatif->nama_alternatif . ' pada kriteria ' . $kriteria->nama_kriteria . ' belum diinput.'
                    ]);
                }
            }

            if (empty($nilaiKriteria)) {
                return view('vikor.hasil', [
                    'error' => 'Nilai kriteria ' . $kriteria->nama_kriteria . ' belum lengkap untuk semua alternatif.'
                ]);
            }

            if ($kriteria->tipe == 'benefit') {
                $fStar[$kriteria->id] = max($nilaiKriteria);
                $fMinus[$kriteria->id] = min($nilaiKriteria);
            } else { // cost
                $fStar[$kriteria->id] = min($nilaiKriteria);
                $fMinus[$kriteria->id] = max($nilaiKriteria);
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

                if (($f_star - $f_minus) == 0) {
                    $norm_nilai = 0; // Hindari pembagian nol
                } else {
                    $norm_nilai = ($f_star - $nilai) / ($f_star - $f_minus);
                }

                $s_val += $bobot * $norm_nilai;
                $r_val = max($r_val, $bobot * $norm_nilai);
            }
            $Si[$alternatif->id] = $s_val;
            $Ri[$alternatif->id] = $r_val;
        }

        // 3. Hitung Qj
        $sMin = min($Si);
        $sMax = max($Si);
        $rMin = min($Ri);
        $rMax = max($Ri);

        // Parameter V (Bobot strategi mayoritas/konsensus)
        // Anda bisa membuat ini configurable di UI atau di .env
        $v = 0.5; // Umumnya 0.5

        $Qi = [];
        foreach ($alternatifs as $alternatif) {
            if (($sMax - $sMin) == 0) { // Hindari pembagian nol
                $qi_s = 0;
            } else {
                $qi_s = ($Si[$alternatif->id] - $sMin) / ($sMax - $sMin);
            }

            if (($rMax - $rMin) == 0) { // Hindari pembagian nol
                $qi_r = 0;
            } else {
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
                'id' => $alternatif->id, // Tambahkan ID untuk referensi
            ];
        }

        // Urutkan berdasarkan nilai Qi (semakin kecil, semakin baik)
        usort($ranking, function ($a, $b) {
            return $a['Qi'] <=> $b['Qi'];
        });

        // Kondisi Penerimaan (Opsional, tergantung implementasi VIKOR Anda)
        // Kondisi 1: A1 lebih baik dari A2, dan Q(A2) - Q(A1) >= DQ
        // DQ = 1 / (m-1) dimana m adalah jumlah alternatif
        $m = count($alternatifs);
        $DQ = ($m > 1) ? (1 / ($m - 1)) : 0; // Hindari pembagian nol

        $kandidatTerbaik = null;
        if (count($ranking) > 0) {
            $kandidatTerbaik = $ranking[0];

            if (count($ranking) > 1) {
                $Q1 = $ranking[0]['Qi'];
                $Q2 = $ranking[1]['Qi'];

                // Kondisi 1: C1 = Q(A2) - Q(A1) >= DQ
                $condition1 = ($Q2 - $Q1) >= $DQ;

                // Kondisi 2: Stabilitas (A1 harus menjadi yang terbaik untuk S atau R)
                $alternatif1 = Alternatif::find($ranking[0]['id']);
                $alternatif2 = Alternatif::find($ranking[1]['id']);

                $sSorted = collect($Si)->sort()->keys()->toArray();
                $rSorted = collect($Ri)->sort()->keys()->toArray();

                $alternatif1IsBestForS = ($sSorted[0] == $alternatif1->id);
                $alternatif1IsBestForR = ($rSorted[0] == $alternatif1->id);

                $condition2 = ($alternatif1IsBestForS || $alternatif1IsBestForR);


                if ($condition1 && $condition2) {
                    $kandidatTerbaik['status'] = 'A1 adalah solusi kompromi terbaik.';
                } elseif (!$condition1) {
                    $kandidatTerbaik['status'] = 'Tidak ada solusi kompromi yang stabil. Ada beberapa solusi setara.';
                    // Biasanya, set solusi kompromi: A1, ..., Am' jika Q(Am') - Q(A1) < DQ
                    // Implementasi ini bisa lebih kompleks jika Anda ingin menampilkan semua solusi setara
                } elseif (!$condition2) {
                    $kandidatTerbaik['status'] = 'Tidak ada solusi kompromi yang stabil. A1 tidak memenuhi kondisi stabilitas.';
                } else {
                    $kandidatTerbaik['status'] = 'Solusi kompromi ditemukan.';
                }
            } else {
                $kandidatTerbaik['status'] = 'Hanya ada satu alternatif.';
            }
        } else {
            $kandidatTerbaik = ['status' => 'Tidak ada alternatif untuk dihitung.'];
        }

        return view('vikor.hasil', compact('kriterias', 'alternatifs', 'fStar', 'fMinus', 'Si', 'Ri', 'Qi', 'ranking', 'kandidatTerbaik', 'DQ'));
    }
}
