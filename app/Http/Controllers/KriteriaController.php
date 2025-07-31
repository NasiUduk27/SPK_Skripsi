<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\NilaiAlternatif;
use Illuminate\Http\Request;

class KriteriaController extends Controller
{
    /**
     * Menampilkan daftar semua kriteria.
     */
    public function index()
    {
        $kriterias = Kriteria::orderBy('id', 'asc')->get();
        // Variabel $totalBobot dihapus karena tidak lagi relevan secara global
        return view('kriteria.index', compact('kriterias'));
    }

    /**
     * Menampilkan form untuk membuat kriteria baru.
     */
    public function create()
    {
        return view('kriteria.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kriteria' => 'required|string|max:255|unique:kriterias,nama_kriteria',
            'tipe' => 'required|in:cost,benefit',
            'bobot' => 'required|numeric|min:0', // Bobot kini menjadi nilai kepentingan relatif
        ]);

        Kriteria::create($request->all());

        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil ditambahkan!');
    }

    public function edit(Kriteria $kriterium)
    {
        return view('kriteria.edit', compact('kriterium'));
    }

    public function update(Request $request, Kriteria $kriterium)
    {
        $request->validate([
            'nama_kriteria' => 'required|string|max:255|unique:kriterias,nama_kriteria,' . $kriterium->id,
            'tipe' => 'required|in:cost,benefit',
            'bobot' => 'required|numeric|min:0', // Bobot kini menjadi nilai kepentingan relatif
        ]);

        $kriterium->update($request->all());

        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil diperbarui!');
    }

    public function destroy(Kriteria $kriterium)
    {
        // Cek apakah kriteria ini sudah digunakan di tabel nilai_alternatifs
        if (NilaiAlternatif::where('kriteria_id', $kriterium->id)->exists()) {
            return redirect()->route('kriteria.index')->with('error', 'Tidak bisa menghapus kriteria karena sudah digunakan dalam penilaian alternatif.');
        }

        $kriterium->delete();
        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil dihapus!');
    }
}
