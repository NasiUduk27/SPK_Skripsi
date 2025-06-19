@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Daftar Kriteria</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('kriteria.create') }}" class="btn btn-sm btn-outline-secondary">Tambah Kriteria</a>
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
                <th>Nama Kriteria</th>
                <th>Tipe</th>
                <th>Bobot</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($kriterias as $kriteria)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $kriteria->nama_kriteria }}</td>
                <td>{{ ucfirst($kriteria->tipe) }}</td>
                <td>{{ $kriteria->bobot }}</td>
                <td>
                    <a href="{{ route('kriteria.edit', $kriteria->id) }}" class="btn btn-info btn-sm">Edit</a>
                    <form action="{{ route('kriteria.destroy', $kriteria->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Anda yakin ingin menghapus kriteria ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5">Tidak ada kriteria.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
