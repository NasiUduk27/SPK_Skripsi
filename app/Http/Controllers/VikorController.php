<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use Illuminate\Http\Request;

class VikorController extends Controller
{
    public function pilihKriteria()
    {
        $kriterias = Kriteria::orderBy('nama_kriteria', 'asc')->get();
        return view('vikor.pilih-kriteria', compact('kriterias'));
    }

    public function hitung(Request $request)
    {
        // =======================================================================
        // LANGKAH 1: VALIDASI DAN PERSIAPAN KRITERIA DINAMIS
        // =======================================================================

        $request->validate([
            'kriteria_ids' => 'required|array|min:2',
            'kriteria_ids.*' => 'exists:kriterias,id',
            'metode_bobot' => 'required|in:sama,prioritas',
            'prioritas' => 'required_if:metode_bobot,prioritas|array',
            'prioritas.*' => 'distinct',
        ], [
            'kriteria_ids.required' => 'Anda harus memilih kriteria terlebih dahulu.',
            'kriteria_ids.min' => 'Harap pilih setidaknya 2 kriteria untuk melakukan perbandingan.',
            'metode_bobot.required' => 'Silakan pilih salah satu metode pembobotan.',
            'prioritas.required_if' => 'Urutan prioritas wajib diisi jika Anda memilih metode bobot berdasarkan prioritas.',
            'prioritas.distinct' => 'Setiap kriteria harus memiliki nomor prioritas yang unik.'
        ]);

        // =======================================================================
        // LANGKAH 2: PERSIAPAN KRITERIA DAN PEMBOBOTAN
        // =======================================================================

        $selectedKriteriaIds = $request->input('kriteria_ids');
        $kriterias = Kriteria::whereIn('id', $selectedKriteriaIds)->get();

        if ($request->metode_bobot == 'sama') {
            $jumlahKriteria = $kriterias->count();
            $bobot = 1 / $jumlahKriteria;
            $kriterias->each(function ($kriteria) use ($bobot) {
                $kriteria->bobot_normalisasi = $bobot;
            });
        } else {
            $prioritas = $request->input('prioritas');
            $n = count($prioritas);
            $sum_of_ranks = $n * ($n + 1) / 2;

            $kriterias->each(function ($kriteria) use ($prioritas, $n, $sum_of_ranks) {
                $rank = $prioritas[$kriteria->id];
                $kriteria->bobot_normalisasi = ($n - $rank + 1) / $sum_of_ranks;
            });
        }

        // =======================================================================
        // LANGKAH 3: PROSES PERHITUNGAN VIKOR
        // =======================================================================

        $alternatifs = Alternatif::with(['nilaiAlternatifs' => function ($query) use ($selectedKriteriaIds) {
            $query->whereIn('kriteria_id', $selectedKriteriaIds);
        }])->get();

        if ($alternatifs->isEmpty()) {
            return view('vikor.hasil', ['error' => 'Data alternatif belum ada. Harap lengkapi terlebih dahulu.']);
        }

        foreach ($alternatifs as $alternatif) {
            if ($alternatif->nilaiAlternatifs->count() < $kriterias->count()) {
                return view('vikor.hasil', [
                    'error' => 'Nilai untuk alternatif "' . $alternatif->nama_alternatif . '" belum lengkap untuk semua kriteria yang Anda pilih.'
                ]);
            }
        }

        // 4. Hitung F* (Nilai Ideal Positif) dan F-
        $fStar = [];
        $fMinus = [];
        foreach ($kriterias as $kriteria) {
            $nilaiList = $alternatifs->map(fn($alt) => $alt->getNilaiByKriteria($kriteria)->nilai);
            if ($kriteria->tipe == 'benefit') {
                $fStar[$kriteria->id] = $nilaiList->max();
                $fMinus[$kriteria->id] = $nilaiList->min();
            } else { // 'cost'
                $fStar[$kriteria->id] = $nilaiList->min();
                $fMinus[$kriteria->id] = $nilaiList->max();
            }
        }

        // 5. Hitung Nilai Si (Utility Measure) dan Ri (Regret Measure)
        $Si = [];
        $Ri = [];
        foreach ($alternatifs as $alternatif) {
            $s_val = 0;
            $r_val_list = [];
            foreach ($kriterias as $kriteria) {
                $nilai = $alternatif->getNilaiByKriteria($kriteria)->nilai;
                $bobot = $kriteria->bobot_normalisasi;
                $fstar_k = $fStar[$kriteria->id];
                $fminus_k = $fMinus[$kriteria->id];

                $denominator = $fstar_k - $fminus_k;
                $normalized_value = (abs($denominator) < 1e-9) ? 0 : (($fstar_k - $nilai) / $denominator);

                $weighted_value = $bobot * $normalized_value;
                $s_val += $weighted_value;
                $r_val_list[] = $weighted_value;
            }
            $Si[$alternatif->id] = $s_val;
            $Ri[$alternatif->id] = max($r_val_list);
        }

        // 6. Hitung Qi (VIKOR Index)
        $sMin = min($Si);
        $sMax = max($Si);
        $rMin = min($Ri);
        $rMax = max($Ri);
        $v = 0.5;
        $Qi = [];

        foreach ($alternatifs as $alternatif) {
            $id = $alternatif->id;
            $s_range = $sMax - $sMin;
            $r_range = $rMax - $rMin;
            $qi_s = ($s_range == 0) ? 0 : ($Si[$id] - $sMin) / $s_range;
            $qi_r = ($r_range == 0) ? 0 : ($Ri[$id] - $rMin) / $r_range;
            $Qi[$id] = ($v * $qi_s) + ((1 - $v) * $qi_r);
        }

        // 7. Perangkingan
        $ranking = collect($alternatifs)->map(function ($alt) use ($Si, $Ri, $Qi) {
            return [
                'id' => $alt->id,
                'alternatif' => $alt->nama_alternatif,
                'Si' => $Si[$alt->id],
                'Ri' => $Ri[$alt->id],
                'Qi' => $Qi[$alt->id],
            ];
        })->sortBy('Qi')->values()->all();

        // 8. Penentuan Solusi Kompromi
        $kandidatTerbaik = $ranking[0] ?? null;
        $DQ = (count($alternatifs) > 1) ? (1 / (count($alternatifs) - 1)) : 0;

        if ($kandidatTerbaik) {
            $statusSolusi = 'Hanya ada satu alternatif.';
            if (count($ranking) > 1) {
                $A1 = $ranking[0];
                $A2 = $ranking[1];
                $condition1 = (abs($A2['Qi'] - $A1['Qi']) >= $DQ);
                $sSortedId = collect($Si)->sort()->keys()->first();
                $rSortedId = collect($Ri)->sort()->keys()->first();
                $condition2 = ($A1['id'] == $sSortedId || $A1['id'] == $rSortedId);

                if ($condition1 && $condition2) {
                    $statusSolusi = 'Solusi kompromi terbaik diterima.';
                } elseif (!$condition1) {
                    $statusSolusi = 'Solusi kompromi tidak jelas (Kondisi 1 tidak terpenuhi).';
                    $kandidatTerbaik['set_solusi_kompromi'] = collect($ranking)->filter(fn($r) => abs($r['Qi'] - $A1['Qi']) < $DQ)->pluck('alternatif')->unique()->values()->all();
                } else { 
                    $statusSolusi = 'Solusi kompromi tidak stabil (Kondisi 2 tidak terpenuhi).';
                    $kandidatTerbaik['set_solusi_kompromi'] = [$A1['alternatif'], $A2['alternatif']];
                }
            }
            $kandidatTerbaik['status'] = $statusSolusi;
        }

        // 9. Tampilkan hasil ke view vikor.hasil
        return view('vikor.hasil', compact(
            'kriterias', 'alternatifs', 'fStar', 'fMinus', 'Si', 'Ri', 'Qi', 'ranking', 'kandidatTerbaik', 'DQ'
        ));
    }
}
