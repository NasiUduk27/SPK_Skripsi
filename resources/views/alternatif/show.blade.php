@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Detail Alternatif: {{ $alternatif->nama_alternatif }}</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('alternatif.inputNilai', $alternatif->id) }}" class="btn btn-sm btn-outline-primary">Input/Edit Nilai Kriteria</a>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>Kriteria</th>
                <th>Tipe</th>
                <th>Bobot</th>
                <th>Nilai</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($kriterias as $kriteria)
            <tr>
                <td>{{ $kriteria->nama_kriteria }}</td>
                <td>{{ ucfirst($kriteria->tipe) }}</td>
                <td>{{ $kriteria->bobot }}</td>
                <td>{{ $nilaiAlternatifs->get($kriteria->id)->nilai ?? 'Belum ada nilai' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4">Tidak ada kriteria yang terdaftar.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<a href="{{ route('alternatif.index') }}" class="btn btn-secondary mt-3">Kembali ke Daftar Alternatif</a>
@endsection