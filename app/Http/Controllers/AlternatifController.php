<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\NilaiAlternatif;
use Illuminate\Http\Request;

class AlternatifController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $alternatifs = Alternatif::all();
        return view('alternatif.index', compact('alternatifs'));
    }

    public function create()
    {
        return view('alternatif.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_alternatif' => 'required|string|max:255|unique:alternatifs',
        ]);

        Alternatif::create($request->all());
        return redirect()->route('alternatif.index')->with('success', 'Alternatif berhasil ditambahkan!');
    }

    public function show(Alternatif $alternatif)
    {
        // Untuk menampilkan nilai-nilai alternatif pada kriteria
        $kriterias = Kriteria::all();
        $nilaiAlternatifs = $alternatif->nilaiAlternatifs->keyBy('kriteria_id');
        return view('alternatif.show', compact('alternatif', 'kriterias', 'nilaiAlternatifs'));
    }

    public function edit(Alternatif $alternatif)
    {
        return view('alternatif.edit', compact('alternatif'));
    }

    public function update(Request $request, Alternatif $alternatif)
    {
        $request->validate([
            'nama_alternatif' => 'required|string|max:255|unique:alternatifs,nama_alternatif,' . $alternatif->id,
        ]);

        $alternatif->update($request->all());
        return redirect()->route('alternatif.index')->with('success', 'Alternatif berhasil diperbarui!');
    }

    public function destroy(Alternatif $alternatif)
    {
        $alternatif->delete(); // Otomatis menghapus nilai terkait karena cascade di migrasi
        return redirect()->route('alternatif.index')->with('success', 'Alternatif berhasil dihapus!');
    }

    // --- Metode untuk input nilai alternatif per kriteria ---
    public function inputNilai(Alternatif $alternatif)
    {
        $kriterias = Kriteria::all();
        $nilaiAlternatifs = $alternatif->nilaiAlternatifs->keyBy('kriteria_id');
        return view('alternatif.input_nilai', compact('alternatif', 'kriterias', 'nilaiAlternatifs'));
    }

    public function simpanNilai(Request $request, Alternatif $alternatif)
    {
        $kriterias = Kriteria::all();
        $rules = [];
        foreach ($kriterias as $kriteria) {
            $rules['nilai_' . $kriteria->id] = 'required|numeric|min:0';
        }
        $request->validate($rules);

        foreach ($kriterias as $kriteria) {
            NilaiAlternatif::updateOrCreate(
                [
                    'alternatif_id' => $alternatif->id,
                    'kriteria_id' => $kriteria->id,
                ],
                [
                    'nilai' => $request->input('nilai_' . $kriteria->id)
                ]
            );
        }

        return redirect()->route('alternatif.show', $alternatif)->with('success', 'Nilai alternatif berhasil disimpan!');
    }
}