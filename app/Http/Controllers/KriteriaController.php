<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Import model User untuk type hinting

class KriteriaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Metode index dan show bisa diakses semua user untuk melihat kriteria global
    public function index()
    {
        $kriterias = Kriteria::all();
        return view('kriteria.index', compact('kriterias'));
    }

    public function show(Kriteria $kriteria)
    {
        return view('kriteria.show', compact('kriteria'));
    }

    // Metode create, store, edit, update, destroy hanya untuk admin
    public function create()
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses untuk menambah kriteria.');
        }
        return view('kriteria.create');
    }

    public function store(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses untuk menyimpan kriteria.');
        }
        $request->validate([
            'nama_kriteria' => 'required|string|max:255',
            'tipe' => 'required|in:cost,benefit',
            'bobot' => 'required|numeric|min:0|max:1',
        ]);

        Kriteria::create($request->all());

        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil ditambahkan!');
    }

    public function edit(Kriteria $kriteria)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit kriteria ini.');
        }
        return view('kriteria.edit', compact('kriteria'));
    }

    public function update(Request $request, Kriteria $kriteria)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses untuk memperbarui kriteria ini.');
        }
        $request->validate([
            'nama_kriteria' => 'required|string|max:255',
            'tipe' => 'required|in:cost,benefit',
            'bobot' => 'required|numeric|min:0|max:1',
        ]);

        $kriteria->update($request->all());

        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil diperbarui!');
    }

    public function destroy(Kriteria $kriteria)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus kriteria ini.');
        }
        if ($kriteria->nilaiAlternatifs()->count() > 0) {
            return redirect()->route('kriteria.index')->with('error', 'Tidak bisa menghapus kriteria karena ada nilai alternatif yang terkait.');
        }

        $kriteria->delete();
        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil dihapus!');
    }
}
