<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\NilaiAlternatif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlternatifController extends Controller
{

    public function index()
    {
        $alternatifs = Alternatif::orderBy('nama_alternatif', 'asc')->get();
        return view('alternatif.index', compact('alternatifs'));
    }

    public function create()
    {
        return view('alternatif.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_alternatif' => 'required|string|max:255|unique:alternatifs,nama_alternatif',
        ]);

        Alternatif::create($request->only('nama_alternatif'));

        return redirect()->route('alternatif.index')->with('success', 'Alternatif berhasil ditambahkan!');
    }

    public function show(Alternatif $alternatif)
    {
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

        $alternatif->update($request->only('nama_alternatif'));
        return redirect()->route('alternatif.index')->with('success', 'Alternatif berhasil diperbarui!');
    }

    public function destroy(Alternatif $alternatif)
    {
        $alternatif->delete();
        return redirect()->route('alternatif.index')->with('success', 'Alternatif berhasil dihapus!');
    }

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
            $rules['nilai_' . $kriteria->id] = 'required|numeric';
        }
        $request->validate($rules);

        DB::beginTransaction();
        try {
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
            DB::commit();

            // PERUBAHAN DI SINI: Redirect ke halaman daftar alternatif (index)
            return redirect()->route('alternatif.index')
                ->with('success', 'Nilai untuk alternatif "' . $alternatif->nama_alternatif . '" berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menyimpan nilai alternatif: ' . $e->getMessage())
                ->withInput();
        }
    }
}
