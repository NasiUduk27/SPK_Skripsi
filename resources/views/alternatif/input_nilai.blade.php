@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Input Nilai untuk Alternatif: {{ $alternatif->nama_alternatif }}</h1>
</div>

<form action="{{ route('alternatif.simpanNilai', $alternatif->id) }}" method="POST">
    @csrf
    @forelse ($kriterias as $kriteria)
    <div class="mb-3">
        <label for="nilai_{{ $kriteria->id }}" class="form-label">{{ $kriteria->nama_kriteria }} ({{ ucfirst($kriteria->tipe) }})</label>
        <input type="number" step="0.01" class="form-control @error('nilai_' . $kriteria->id) is-invalid @enderror" id="nilai_{{ $kriteria->id }}" name="nilai_{{ $kriteria->id }}" value="{{ old('nilai_' . $kriteria->id, $nilaiAlternatifs->get($kriteria->id)->nilai ?? '') }}" required>
        @error('nilai_' . $kriteria->id)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    @empty
    <div class="alert alert-warning">Tidak ada kriteria yang terdaftar. Harap tambahkan kriteria terlebih dahulu.</div>
    @endforelse

    @if ($kriterias->count() > 0)
        <button type="submit" class="btn btn-primary">Simpan Nilai</button>
    @endif
    <a href="{{ route('alternatif.show', $alternatif->id) }}" class="btn btn-secondary">Batal</a>
</form>
@endsection