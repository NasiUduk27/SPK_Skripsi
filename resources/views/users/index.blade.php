@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Kelola Pengguna</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('users.create') }}" class="btn btn-sm btn-outline-secondary">Tambah Pengguna Baru</a>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $userItem)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $userItem->name }}</td>
                <td>{{ $userItem->email }}</td>
                <td>
                    @if($userItem->isAdmin())
                        <span class="badge bg-primary">Admin</span>
                    @else
                        <span class="badge bg-secondary">User Biasa</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('users.edit', $userItem->id) }}" class="btn btn-info btn-sm">Edit</a>
                    <form action="{{ route('users.destroy', $userItem->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Anda yakin ingin menghapus pengguna ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5">Tidak ada pengguna terdaftar.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
