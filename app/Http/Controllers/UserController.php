<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->isAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses sebagai Administrator untuk mengelola user.');
        }
        $users = User::all(); 
        return view('users.index', compact('users'));
    }

    public function create()
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->isAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses sebagai Administrator untuk menambah user.');
        }
        return view('users.create');
    }

    public function store(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->isAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses sebagai Administrator untuk menyimpan user.');
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'is_admin' => 'boolean',
        ]);
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $request->has('is_admin') ? true : false,
        ]);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan!');
    }

    public function show(User $user)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();

        if (!$loggedInUser->isAdmin() && $loggedInUser->id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat profil pengguna ini.');
        }
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();

        if (!$loggedInUser->isAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses sebagai Administrator untuk mengedit user.');
        }
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();

        if (!$loggedInUser->isAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses sebagai Administrator untuk memperbarui user.');
        }
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'is_admin' => 'boolean',
        ];
        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8|confirmed';
        }

        $request->validate($rules);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'is_admin' => $request->has('is_admin') ? true : false,
        ];
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();

        if (!$loggedInUser->isAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses sebagai Administrator untuk menghapus user.');
        }
        if ($user->isAdmin()) {
             // Cek apakah ini satu-satunya admin
            $adminCount = User::where('is_admin', true)->count();
            if ($adminCount <= 1 && $user->id === $loggedInUser->id) {
                return redirect()->route('users.index')->with('error', 'Anda tidak dapat menghapus akun admin terakhir.');
            }
        }
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus!');
    }
}
