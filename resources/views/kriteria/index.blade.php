@extends('layouts.app')

@section('page_title', 'Kelola Kriteria')

@section('content')
<div class="card">
    <div class="card-header pb-0">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bolder">Daftar Kriteria</h4>
            {{-- Tombol untuk menambah kriteria baru --}}
            <a href="{{ route('kriteria.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Tambah Kriteria
            </a>
        </div>
    </div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success text-white">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger text-white">{{ session('error') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th class="text-white">#</th>
                        <th class="text-white">Nama Kriteria</th>
                        <th class="text-white">Tipe</th>
                        <th class="text-white">Bobot Relatif</th>
                        <th class="text-white">Aksi</th>
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
                            <a href="{{ route('kriteria.edit', $kriteria->id) }}" class="btn btn-warning btn-sm mb-0">Edit</a>
                            <form action="{{ route('kriteria.destroy', $kriteria->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm mb-0" onclick="return confirm('Anda yakin ingin menghapus kriteria ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada kriteria. Silakan tambahkan kriteria baru.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
