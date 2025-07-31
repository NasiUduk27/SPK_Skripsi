<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use Illuminate\Http\Request;

class KriteriaController extends Controller
{

    public function index()
    {
        $kriterias = Kriteria::orderBy('nama_kriteria', 'asc')->get();
        return view('kriteria.index', compact('kriterias'));
    }

    public function prosesPemilihan(Request $request)
    {
        $request->validate([
            'kriteria_ids' => 'required|array|min:2',
        ], [
            'kriteria_ids.required' => 'Anda harus memilih minimal 2 kriteria untuk melanjutkan.',
        ]);

        $request->session()->put('selected_kriteria_ids', $request->input('kriteria_ids'));

        return redirect()->route('alternatif.index')->with('success', 'Kriteria berhasil dipilih! Sekarang, silakan input nilai untuk setiap alternatif.');
    }
}
