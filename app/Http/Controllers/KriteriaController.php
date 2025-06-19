<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Validation\ValidationException; 

class KriteriaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $kriterias = Kriteria::all();
        $totalBobot = $kriterias->sum('bobot');
        return view('kriteria.index', compact('kriterias', 'totalBobot'));
    }

    public function show(Kriteria $kriteria)
    {
        return view('kriteria.show', compact('kriteria'));
    }

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

        $currentTotalBobot = Kriteria::sum('bobot');
        $potentialNewTotal = $currentTotalBobot + $request->bobot;
        $epsilon = 0.01;

        if (round($potentialNewTotal, 2) > 1.0 + $epsilon) {
             throw ValidationException::withMessages([
                'bobot' => ['Penambahan kriteria ini akan membuat total bobot melebihi 1.0. Total saat ini: ' . number_format($currentTotalBobot, 2)],
            ]);
        }

        Kriteria::create($request->all());

        $updatedTotalBobot = Kriteria::sum('bobot');
        if (abs($updatedTotalBobot - 1.0) > $epsilon) {
            if ($updatedTotalBobot < 1.0) {
                return redirect()->route('kriteria.index')->with('warning', 'Total bobot kriteria saat ini adalah ' . number_format($updatedTotalBobot, 2) . '. Harap sesuaikan bobot kriteria Anda agar totalnya tepat 1.0 untuk perhitungan VIKOR.');
            }
        }

        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil ditambahkan!');
    }

    public function edit(Kriteria $kriterium)
    {
        /** @var User $user */
        $user = Auth::user();
        return view('kriteria.edit', compact('kriterium'));
    }

    public function update(Request $request, Kriteria $kriterium)
    {
        /** @var User $user */
        $user = Auth::user();

        $rules = [
            'bobot' => 'required|numeric|min:0|max:1',
        ];
        if ($user->isAdmin()) {
            $rules['nama_kriteria'] = 'required|string|max:255';
            $rules['tipe'] = 'required|in:cost,benefit';
        } else {

        }

        $request->validate($rules);
        $existingBobotSumExcludingCurrent = Kriteria::where('id', '!=', $kriterium->id)->sum('bobot');
        $newTotalBobot = $existingBobotSumExcludingCurrent + $request->bobot;
        $epsilon = 0.01;

        if (round($newTotalBobot, 2) > 1.0 + $epsilon) {
             throw ValidationException::withMessages([
                'bobot' => ['Perubahan ini akan membuat total bobot melebihi 1.0. Total saat ini: ' . number_format($existingBobotSumExcludingCurrent, 2)],
            ]);
        }
        if ($user->isAdmin()) {
            $kriterium->update($request->all());
        } else {
            $kriterium->update([
                'bobot' => $request->bobot,
            ]);
        }

        $updatedTotalBobot = Kriteria::sum('bobot');
        if (abs($updatedTotalBobot - 1.0) > $epsilon) {
            if ($updatedTotalBobot < 1.0) {
                return redirect()->route('kriteria.index')->with('warning', 'Total bobot kriteria saat ini adalah ' . number_format($updatedTotalBobot, 2) . '. Harap sesuaikan bobot kriteria Anda agar totalnya tepat 1.0 untuk perhitungan VIKOR.');
            }
        }

        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil diperbarui!');
    }

    public function destroy(Kriteria $kriterium)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus kriteria ini.');
        }
        if ($kriterium->nilaiAlternatifs()->count() > 0) {
            return redirect()->route('kriteria.index')->with('error', 'Tidak bisa menghapus kriteria karena ada nilai alternatif yang terkait.');
        }

        $kriterium->delete();
        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil dihapus!');
    }
}
