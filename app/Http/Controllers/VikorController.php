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

        if ($kriterias->isEmpty() || ($user->isAdmin() ? Alternatif::count() == 0 : $user->alternatifs()->count() == 0)) {
            return view('vikor.hasil', [
                'error' => 'Data kriteria atau alternatif belum lengkap. Harap lengkapi terlebih dahulu.'
            ]);
        }

        $alternatifs = $user->isAdmin()
            ? Alternatif::with('nilaiAlternatifs')->get()
            : $user->alternatifs()->with('nilaiAlternatifs')->get();

        foreach ($alternatifs as $alternatif) {
            foreach ($kriterias as $kriteria) {
                if (!$alternatif->getNilaiByKriteria($kriteria)) {
                    return view('vikor.hasil', [
                        'error' => 'Nilai untuk ' . $alternatif->nama_alternatif . ' pada kriteria ' . $kriteria->nama_kriteria . ' belum diinput.'
                    ]);
                }
            }
        }

        // 1. Hitung F* dan F-
        $fStar = [];
        $fMinus = [];

        foreach ($kriterias as $kriteria) {
            $nilaiList = $alternatifs->map(fn($alt) => $alt->getNilaiByKriteria($kriteria)->nilai);

            if ($kriteria->tipe == 'benefit') {
                $fStar[$kriteria->id] = $nilaiList->max();
                $fMinus[$kriteria->id] = $nilaiList->min();
            } else {
                $fStar[$kriteria->id] = $nilaiList->min();
                $fMinus[$kriteria->id] = $nilaiList->max();
            }
        }

        // 2. Hitung Si dan Ri
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
                $denom = abs($fstar - $fminus) < 1e-9 ? 1 : abs($fstar - $fminus);

                if ($kriteria->tipe === 'benefit') {
                    $normalized = ($fstar - $nilai) / $denom;
                } else { // cost
                    $normalized = ($nilai - $fstar) / $denom;
                }

                $weighted = $bobot * $normalized;
                $s_val += $weighted;
                $r_val = max($r_val, $weighted);
            }

            $Si[$alternatif->id] = $s_val;
            $Ri[$alternatif->id] = $r_val;
        }

        // 3. Hitung Qi
        $sMin = min($Si);
        $sMax = max($Si);
        $rMin = min($Ri);
        $rMax = max($Ri);

        $v = 0.5;
        $Qi = [];

        foreach ($alternatifs as $alternatif) {
            $id = $alternatif->id;

            $qi_s = ($sMax - $sMin) == 0 ? 0 : ($Si[$id] - $sMin) / ($sMax - $sMin);
            $qi_r = ($rMax - $rMin) == 0 ? 0 : ($Ri[$id] - $rMin) / ($rMax - $rMin);

            $Qi[$id] = $v * $qi_s + (1 - $v) * $qi_r;
        }

        // 4. Ranking
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

        // 5. Solusi Kompromi
        $kandidatTerbaik = $ranking[0] ?? null;
        $statusSolusi = 'Tidak dapat menentukan solusi kompromi.';
        $DQ = count($ranking) > 1 ? (1 / (count($ranking) - 1)) : 0;

        if ($kandidatTerbaik && count($ranking) > 1) {
            $A1 = $ranking[0];
            $A2 = $ranking[1];
            $condition1 = ($A2['Qi'] - $A1['Qi']) >= $DQ;

            $sSorted = collect($Si)->sort()->keys()->first();
            $rSorted = collect($Ri)->sort()->keys()->first();
            $condition2 = ($A1['id'] == $sSorted || $A1['id'] == $rSorted);

            if ($condition1 && $condition2) {
                $statusSolusi = 'A1 adalah solusi kompromi terbaik.';
            } elseif (!$condition1) {
                $statusSolusi = 'Tidak ada solusi kompromi yang jelas.';
                $set = [$A1['alternatif']];
                foreach ($ranking as $r) {
                    if (abs($r['Qi'] - $A1['Qi']) < $DQ) {
                        $set[] = $r['alternatif'];
                    } else {
                        break;
                    }
                }
                $kandidatTerbaik['set_solusi_kompromi'] = $set;
            } elseif (!$condition2) {
                $statusSolusi = 'Solusi kompromi tidak stabil.';
                $kandidatTerbaik['set_solusi_kompromi'] = [$A1['alternatif'], $A2['alternatif']];
            } else {
                $statusSolusi = 'Solusi kompromi ditemukan.';
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
