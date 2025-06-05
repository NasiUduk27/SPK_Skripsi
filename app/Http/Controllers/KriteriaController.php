<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use Illuminate\Http\Request;

class KriteriaController extends Controller
{
    // Pastikan user sudah login untuk mengakses ini
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $kriterias = Kriteria::all();
        return view('kriteria.index', compact('kriterias'));
    }

    public function create()
    {
        return view('kriteria.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kriteria' => 'required|string|max:255', 
            'tipe' => 'required|in:cost,benefit',
            'bobot' => 'required|numeric|min:0|max:1',
        ]);

        Kriteria::create($request->all());

        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil ditambahkan!');
    }

    public function show(Kriteria $kriteria)
    {
        return view('kriteria.show', compact('kriteria'));
    }

    public function edit(Kriteria $kriteria)
    {
        return view('kriteria.edit', compact('kriteria'));
    }

    public function update(Request $request, Kriteria $kriteria)
    {
        $request->validate([
            'nama_kriteria' => 'required|string|max:255', // Pastikan ini 'nama_kriteria' sesuai di view
            'tipe' => 'required|in:cost,benefit',
            'bobot' => 'required|numeric|min:0|max:1',
        ]);

        // Penting: Pastikan nama kolom di database Anda adalah 'nama_kriteria'
        $kriteria->update($request->all());

        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil diperbarui!');
    }

    public function destroy(Kriteria $kriteria)
    {
        // Pastikan tidak ada nilai alternatif yang terkait sebelum menghapus kriteria
        if ($kriteria->nilaiAlternatifs()->count() > 0) {
            return redirect()->route('kriteria.index')->with('error', 'Tidak bisa menghapus kriteria karena ada nilai alternatif yang terkait.');
        }

        $kriteria->delete();
        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil dihapus!');
    }
}