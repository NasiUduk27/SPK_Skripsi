<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\Penilaian;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    public function index()
    {
        $alternatifs = Alternatif::all();
        $kriterias = Kriteria::all();
        return view('penilaian.index', compact('alternatifs', 'kriterias'));
    }

    public function simpan(Request $request)
    {
        foreach ($request->nilai as $alternatif_id => $kriteria_nilai) {
            foreach ($kriteria_nilai as $kriteria_id => $nilai) {
                Penilaian::updateOrCreate(
                    ['alternatif_id' => $alternatif_id, 'kriteria_id' => $kriteria_id],
                    ['nilai' => $nilai]
                );
            }
        }
        return redirect()->route('penilaian.index');
    }
}
