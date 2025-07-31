<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\NilaiAlternatif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlternatifController extends Controller
{
    public function index(Request $request)
    {
        $selectedIds = $request->session()->get('selected_kriteria_ids');
        if (!$selectedIds) {
            return redirect()->route('kriteria.index')->with('error', 'Silakan tentukan kriteria terlebih dahulu sebelum mengelola alternatif.');
        }
        $kriterias = Kriteria::whereIn('id', $selectedIds)->get();
        $alternatifs = Alternatif::with(['nilaiAlternatifs' => function ($query) use ($selectedIds) {
            $query->whereIn('kriteria_id', $selectedIds);
        }])->orderBy('nama_alternatif', 'asc')->get();
        return view('alternatif.index', compact('alternatifs', 'kriterias'));
    }

    public function simpanDanLanjutkan(Request $request)
    {
        $request->validate([
            'nilai' => 'required|array',
            'nilai.*.*' => 'required|numeric'
        ], [
            'nilai.*.*.required' => 'Semua nilai kriteria wajib diisi sebelum melanjutkan ke perhitungan.'
        ]);

        $allNilai = $request->input('nilai');

        DB::beginTransaction();
        try {
            foreach ($allNilai as $alternatif_id => $kriteria_values) {
                foreach ($kriteria_values as $kriteria_id => $nilai) {
                    NilaiAlternatif::updateOrCreate(
                        ['alternatif_id' => $alternatif_id, 'kriteria_id' => $kriteria_id],
                        ['nilai' => $nilai]
                    );
                }
            }
            DB::commit();

            return redirect()->route('vikor.pilih')->with('success', 'Semua nilai berhasil disimpan! Sekarang, tentukan metode pembobotan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan nilai: ' . $e->getMessage());
        }
    }

    public function create()
    {
        return view('alternatif.create');
    }

    public function store(Request $request)
    {
        $request->validate(['nama_alternatif' => 'required|string|max:255|unique:alternatifs,nama_alternatif']);
        Alternatif::create($request->only('nama_alternatif'));
        return redirect()->route('alternatif.index')->with('success', 'Alternatif baru berhasil ditambahkan! Silakan isi nilainya di bawah.');
    }

    public function edit(Alternatif $alternatif)
    {
        return view('alternatif.edit', compact('alternatif'));
    }

    public function update(Request $request, Alternatif $alternatif)
    {
        $request->validate(['nama_alternatif' => 'required|string|max:255|unique:alternatifs,nama_alternatif,' . $alternatif->id]);
        $alternatif->update($request->only('nama_alternatif'));
        return redirect()->route('alternatif.index')->with('success', 'Nama alternatif berhasil diperbarui!');
    }

    public function destroy(Alternatif $alternatif)
    {
        $alternatif->delete();
        return redirect()->route('alternatif.index')->with('success', 'Alternatif berhasil dihapus!');
    }
}
