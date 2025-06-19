<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Pastikan ini diimpor
use Illuminate\Validation\ValidationException; // Pastikan ini diimpor

class KriteriaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $kriterias = Kriteria::all();
        $totalBobot = $kriterias->sum('bobot'); // Hitung total bobot di sini
        return view('kriteria.index', compact('kriterias', 'totalBobot')); // Teruskan totalBobot ke view
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
        $epsilon = 0.01; // Toleransi untuk perbandingan floating point (misalnya 0.01)

        // Validasi: Tidak boleh melebihi 1.0
        if (round($potentialNewTotal, 2) > 1.0 + $epsilon) {
             throw ValidationException::withMessages([
                'bobot' => ['Penambahan kriteria ini akan membuat total bobot melebihi 1.0. Total saat ini: ' . number_format($currentTotalBobot, 2)],
            ]);
        }

        Kriteria::create($request->all());

        // Setelah berhasil menyimpan, cek total bobot untuk pesan peringatan
        $updatedTotalBobot = Kriteria::sum('bobot');
        if (abs($updatedTotalBobot - 1.0) > $epsilon) { // Jika total bobot tidak tepat 1.0 (dengan toleransi)
            if ($updatedTotalBobot < 1.0) {
                // Beri pesan peringatan jika total kurang dari 1.0
                return redirect()->route('kriteria.index')->with('warning', 'Total bobot kriteria saat ini adalah ' . number_format($updatedTotalBobot, 2) . '. Harap sesuaikan bobot kriteria Anda agar totalnya tepat 1.0 untuk perhitungan VIKOR.');
            }
            // Kasus updatedTotalBobot > 1.0 akan ditangkap oleh validasi di atas
        }

        // Jika total bobot tepat 1.0 atau sangat mendekati, berikan pesan sukses biasa
        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil ditambahkan!');
    }

    public function show(Kriteria $kriteria)
    {
        return view('kriteria.show', compact('kriteria'));
    }

    public function edit(Kriteria $kriterium)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit kriteria ini.');
        }
        return view('kriteria.edit', compact('kriterium'));
    }

    public function update(Request $request, Kriteria $kriterium)
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

        $existingBobotSumExcludingCurrent = Kriteria::where('id', '!=', $kriterium->id)->sum('bobot');
        $newTotalBobot = $existingBobotSumExcludingCurrent + $request->bobot;
        $epsilon = 0.01; // Toleransi untuk perbandingan floating point

        // Validasi: Tidak boleh melebihi 1.0
        if (round($newTotalBobot, 2) > 1.0 + $epsilon) {
             throw ValidationException::withMessages([
                'bobot' => ['Perubahan ini akan membuat total bobot melebihi 1.0. Total saat ini: ' . number_format($existingBobotSumExcludingCurrent, 2)],
            ]);
        }

        $kriterium->update($request->all());

        // Setelah berhasil update, cek total bobot untuk pesan peringatan
        $updatedTotalBobot = Kriteria::sum('bobot');
        if (abs($updatedTotalBobot - 1.0) > $epsilon) { // Jika total bobot tidak tepat 1.0 (dengan toleransi)
            if ($updatedTotalBobot < 1.0) {
                // Beri pesan peringatan jika total kurang dari 1.0
                return redirect()->route('kriteria.index')->with('warning', 'Total bobot kriteria saat ini adalah ' . number_format($updatedTotalBobot, 2) . '. Harap sesuaikan bobot kriteria Anda agar totalnya tepat 1.0 untuk perhitungan VIKOR.');
            }
        }

        // Jika total bobot tepat 1.0 atau sangat mendekati, berikan pesan sukses biasa
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
