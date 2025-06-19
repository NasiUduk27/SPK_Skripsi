<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\NilaiAlternatif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Import model User untuk type hinting

class AlternatifController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->isAdmin()) {
            $alternatifs = Alternatif::all();
        } else {
            $alternatifs = $user->alternatifs;
        }
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

        Alternatif::create([
            'nama_alternatif' => $request->nama_alternatif,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('alternatif.index')->with('success', 'Alternatif berhasil ditambahkan!');
    }

    public function show(Alternatif $alternatif)
    {
        /** @var User $user */
        $user = Auth::user();
        if ($user->id !== $alternatif->user_id && !$user->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses ke alternatif ini.');
        }
        $kriterias = Kriteria::all();
        $nilaiAlternatifs = $alternatif->nilaiAlternatifs->keyBy('kriteria_id');
        return view('alternatif.show', compact('alternatif', 'kriterias', 'nilaiAlternatifs'));
    }

    public function edit(Alternatif $alternatif)
    {
        /** @var User $user */
        $user = Auth::user();
        if ($user->id !== $alternatif->user_id && !$user->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit alternatif ini.');
        }
        return view('alternatif.edit', compact('alternatif'));
    }

    public function update(Request $request, Alternatif $alternatif)
    {
        /** @var User $user */
        $user = Auth::user();
        if ($user->id !== $alternatif->user_id && !$user->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses untuk memperbarui alternatif ini.');
        }
        $request->validate([
            'nama_alternatif' => 'required|string|max:255|unique:alternatifs,nama_alternatif,' . $alternatif->id,
        ]);

        $alternatif->update($request->all());
        return redirect()->route('alternatif.index')->with('success', 'Alternatif berhasil diperbarui!');
    }

    public function destroy(Alternatif $alternatif)
    {
        /** @var User $user */
        $user = Auth::user();
        $alternatif->delete();
        return redirect()->route('alternatif.index')->with('success', 'Alternatif berhasil dihapus!');
    }
    public function inputNilai(Alternatif $alternatif)
    {
        /** @var User $user */
        $user = Auth::user();
        if ($user->id !== $alternatif->user_id && !$user->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses untuk menginput nilai alternatif ini.');
        }
        $kriterias = Kriteria::all();
        $nilaiAlternatifs = $alternatif->nilaiAlternatifs->keyBy('kriteria_id');
        return view('alternatif.input_nilai', compact('alternatif', 'kriterias', 'nilaiAlternatifs'));
    }

    public function simpanNilai(Request $request, Alternatif $alternatif)
    {
        /** @var User $user */
        $user = Auth::user();
        if ($user->id !== $alternatif->user_id && !$user->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses untuk menyimpan nilai alternatif ini.');
        }
        $kriterias = Kriteria::all();
        $rules = [];
        foreach ($kriterias as $kriteria) {
            $rules['nilai_' . $kriteria->id] = 'required|in:2,6,9';
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

            return redirect()->route('alternatif.show', $alternatif)
                ->with('success', 'Nilai alternatif berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menyimpan nilai alternatif: ' . $e->getMessage())
                ->withInput();
        }
    }
}
