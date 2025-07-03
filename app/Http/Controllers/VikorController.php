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

        // Validasi: Total bobot kriteria harus 1.00 untuk pengguna biasa
        if (!$user->isAdmin() && abs($totalBobot - 1.0) > $epsilon) {
            return view('vikor.hasil', [
                'error' => 'Total bobot kriteria saat ini adalah ' . number_format($totalBobot, 2) . '. Harap sesuaikan bobot kriteria agar totalnya tepat 1.0 sebelum melakukan perhitungan VIKOR.'
            ]);
        }

        // Ambil alternatif berdasarkan peran pengguna
        $alternatifs = $user->isAdmin()
            ? Alternatif::with('nilaiAlternatifs')->get()
            : $user->alternatifs()->with('nilaiAlternatifs')->get();

        // Validasi: Pastikan ada kriteria dan alternatif
        if ($kriterias->isEmpty() || $alternatifs->isEmpty()) {
            return view('vikor.hasil', [
                'error' => 'Data kriteria atau alternatif belum lengkap. Harap lengkapi terlebih dahulu.'
            ]);
        }

        // Validasi: Pastikan semua nilai alternatif untuk semua kriteria sudah diinput
        foreach ($alternatifs as $alternatif) {
            foreach ($kriterias as $kriteria) {
                if (!$alternatif->getNilaiByKriteria($kriteria)) {
                    return view('vikor.hasil', [
                        'error' => 'Nilai untuk ' . $alternatif->nama_alternatif . ' pada kriteria ' . $kriteria->nama_kriteria . ' belum diinput. Harap lengkapi nilai semua alternatif untuk semua kriteria.'
                    ]);
                }
            }
        }

        // 1. Hitung F* (Nilai Ideal Positif) dan F- (Nilai Ideal Negatif)
        $fStar = [];
        $fMinus = [];

        foreach ($kriterias as $kriteria) {
            $nilaiList = $alternatifs->map(fn($alt) => $alt->getNilaiByKriteria($kriteria)->nilai)->filter()->values(); // Pastikan nilaiList tidak kosong dan di-reset index-nya

            if ($nilaiList->isEmpty()) { // Handle case where no values exist for a criterion (should be caught by previous validation but as a safeguard)
                return view('vikor.hasil', [
                    'error' => 'Tidak ada nilai yang ditemukan untuk kriteria ' . $kriteria->nama_kriteria . '. Harap periksa input nilai alternatif.'
                ]);
            }

            if ($kriteria->tipe == 'benefit') {
                $fStar[$kriteria->id] = $nilaiList->max();
                $fMinus[$kriteria->id] = $nilaiList->min();
            } else { // cost
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

                // Hitung denominator. Jika fstar dan fminus sama, set denominator ke 1 untuk menghindari division by zero,
                // dan norm_nilai akan menjadi 0.
                $denom = ($fstar - $fminus);
                if (abs($denom) < 1e-9) { // Menggunakan epsilon untuk perbandingan float
                    $normalized = 0;
                } else {
                    if ($kriteria->tipe === 'benefit') {
                        $normalized = ($fstar - $nilai) / $denom;
                    } else { // cost
                        // Untuk cost, fstar adalah min, fminus adalah max.
                        // Rumus (nilai - fstar) / (fminus - fstar) adalah (nilai - min) / (max - min)
                        // Karena $denom = ($fstar - $fminus) = (min - max) = -(max - min)
                        // maka ($nilai - $fstar) / $denom menjadi ($nilai - min) / -(max - min)
                        // yang setara dengan - (($nilai - min) / (max - min))
                        // atau untuk tetap positif dan konsisten dengan benefit: ($nilai - fstar) / (abs(fminus - fstar))
                        // Lebih baik gunakan secara eksplisit:
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
        // Periksa apakah Si atau Ri kosong untuk mencegah error min/max pada array kosong
        if (empty($Si) || empty($Ri)) {
            return view('vikor.hasil', [
                'error' => 'Gagal menghitung nilai Si atau Ri. Pastikan semua alternatif memiliki nilai untuk semua kriteria.'
            ]);
        }

        $sMin = min($Si);
        $sMax = max($Si);
        $rMin = min($Ri);
        $rMax = max($Ri);

        $v = 0.5; // Bobot strategi 'mayoritas' vs 'minoritas penyesalan maksimum'
        $Qi = [];

        foreach ($alternatifs as $alternatif) {
            $id = $alternatif->id;

            // Pastikan tidak ada pembagian dengan nol untuk rentang S dan R
            $sRange = ($sMax - $sMin);
            $qi_s = ($sRange == 0) ? 0 : ($Si[$id] - $sMin) / $sRange;

            $rRange = ($rMax - $rMin);
            $qi_r = ($rRange == 0) ? 0 : ($Ri[$id] - $rMin) / $rRange;

            $Qi[$id] = $v * $qi_s + (1 - $v) * $qi_r;
        }

        // 4. Perangkingan (Sortir berdasarkan Qi ascending)
        $ranking = [];
        foreach ($alternatifs as $alt) {
            $id = $alt->id;
            $ranking[] = [
                'id' => $id,
                'alternatif' => $alt->nama_alternatif,
                'Si' => round($Si[$id], 6), // Pembulatan untuk tampilan
                'Ri' => round($Ri[$id], 6), // Pembulatan untuk tampilan
                'Qi' => round($Qi[$id], 6), // Pembulatan untuk tampilan
            ];
        }

        usort($ranking, fn($a, $b) => $a['Qi'] <=> $b['Qi']);

        // 5. Penentuan Solusi Kompromi
        $kandidatTerbaik = $ranking[0] ?? null;
        $statusSolusi = 'Tidak ada alternatif untuk dihitung.'; // Default jika ranking kosong
        $DQ = 0; // Default nilai DQ

        if (count($ranking) > 0) {
            $statusSolusi = 'Tidak dapat menentukan solusi kompromi.'; // Default jika ada alternatif tapi kondisi tidak terpenuhi
            $DQ = (count($alternatifs) > 1) ? (1 / (count($alternatifs) - 1)) : 0; // Delta Q

            if (count($ranking) > 1) {
                $A1 = $ranking[0]; // Alternatif peringkat pertama
                $A2 = $ranking[1]; // Alternatif peringkat kedua

                // Kondisi 1: Acceptable Advantage (Q(A2) - Q(A1) >= DQ)
                $condition1 = (abs($A2['Qi'] - $A1['Qi']) >= $DQ);

                // Kondisi 2: Acceptable Stability in Decision Making (A1 adalah yang terbaik dari S atau R)
                $sSortedKeys = collect($Si)->sort()->keys()->first(); // ID alternatif dengan S terkecil
                $rSortedKeys = collect($Ri)->sort()->keys()->first(); // ID alternatif dengan R terkecil
                $condition2 = ($A1['id'] == $sSortedKeys || $A1['id'] == $rSortedKeys);

                if ($condition1 && $condition2) {
                    $statusSolusi = 'A1 adalah solusi kompromi terbaik.';
                } elseif (!$condition1) {
                    $statusSolusi = 'Tidak ada solusi kompromi yang jelas.';
                    $setSolusiKompromi = [];
                    // Kumpulkan semua alternatif yang Q-nya sangat dekat dengan A1
                    foreach ($ranking as $r) {
                        // Jika selisih Qi dengan A1 kurang dari DQ, masukkan ke set
                        if (abs($r['Qi'] - $A1['Qi']) < $DQ) {
                            $setSolusiKompromi[] = $r['alternatif'];
                        } else {
                            // Karena sudah diurutkan, jika ada yang di luar toleransi, sisanya juga di luar
                            // Kecuali jika ada nilai Qi yang sama persis (abs diff is 0).
                            // Lebih aman menggunakan abs($r['Qi'] - $A1['Qi']) < $DQ
                            // Ini akan menghentikan pencarian setelah melewati ambang batas.
                             if ($r['Qi'] > $A1['Qi']) { // Hanya break jika Q lebih besar dari A1's Q
                                break;
                            }
                        }
                    }
                    // Pastikan tidak ada duplikasi dan reset index array
                    $kandidatTerbaik['set_solusi_kompromi'] = array_values(array_unique($setSolusiKompromi));
                } elseif (!$condition2) {
                    $statusSolusi = 'Solusi kompromi tidak stabil.';
                    // Set solusi kompromi {A1, A2}
                    $kandidatTerbaik['set_solusi_kompromi'] = array_values(array_unique([$A1['alternatif'], $A2['alternatif']]));
                } else {
                    $statusSolusi = 'Solusi kompromi ditemukan (kondisi non-standar).';
                }
            } else {
                $statusSolusi = 'Hanya ada satu alternatif yang tersedia.';
            }
        }

        // Tambahkan status solusi ke kandidatTerbaik jika ada kandidat terbaik
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
