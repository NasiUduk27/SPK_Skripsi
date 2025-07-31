@extends('layouts.app')

@section('page_title', 'Kelola Alternatif')

@section('content')
<div class="card">
    <div class="card-header pb-0">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bolder">Daftar Alternatif</h4>
            <a href="{{ route('alternatif.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Tambah Alternatif
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
                        <th class="text-white">Nama Alternatif</th>
                        <th class="text-white">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($alternatifs as $alternatif)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $alternatif->nama_alternatif }}</td>
                        <td>
                            <a href="{{ route('alternatif.show', $alternatif->id) }}" class="btn btn-secondary btn-sm mb-0">Lihat Nilai</a>

                            <a href="{{ route('alternatif.inputNilai', $alternatif->id) }}" class="btn btn-info btn-sm mb-0">Input Nilai</a>
                            <a href="{{ route('alternatif.edit', $alternatif->id) }}" class="btn btn-warning btn-sm mb-0">Edit Nama</a>
                            <form action="{{ route('alternatif.destroy', $alternatif->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm mb-0" onclick="return confirm('Anda yakin ingin menghapus alternatif ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada alternatif. Silakan tambahkan alternatif baru.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
