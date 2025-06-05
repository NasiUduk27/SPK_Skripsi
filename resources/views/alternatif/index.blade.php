@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Daftar Alternatif</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('alternatif.create') }}" class="btn btn-sm btn-outline-secondary">Tambah Alternatif</a>
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
                <th>Nama Alternatif</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($alternatifs as $alternatif)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $alternatif->nama_alternatif }}</td>
                <td>
                    <a href="{{ route('alternatif.show', $alternatif->id) }}" class="btn btn-primary btn-sm">Lihat Nilai</a>
                    <a href="{{ route('alternatif.inputNilai', $alternatif->id) }}" class="btn btn-info btn-sm">Input Nilai</a>
                    <a href="{{ route('alternatif.edit', $alternatif->id) }}" class="btn btn-info btn-sm">Edit</a>
                    <form action="{{ route('alternatif.destroy', $alternatif->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Anda yakin ingin menghapus alternatif ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3">Tidak ada alternatif.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection