@extends('layouts.app')

@section('page_title', 'Input Nilai Alternatif')

@section('content')
<div class="card">
    <div class="card-header pb-0">
        <h4 class="font-weight-bolder">Input Nilai untuk: {{ $alternatif->nama_alternatif }}</h4>
        <p class="mb-0">Masukkan nilai untuk setiap kriteria yang tersedia.</p>
    </div>
    <div class="card-body">
        <form action="{{ route('alternatif.simpanNilai', $alternatif->id) }}" method="POST">
            @csrf

            @forelse ($kriterias as $kriteria)
            <div class="mb-3">
                <label for="nilai_{{ $kriteria->id }}" class="form-label">{{ $kriteria->nama_kriteria }} (Tipe: {{ ucfirst($kriteria->tipe) }})</label>
                <select class="form-control @error('nilai_' . $kriteria->id) is-invalid @enderror" id="nilai_{{ $kriteria->id }}" name="nilai_{{ $kriteria->id }}" required>
                    <option value="" disabled {{ old('nilai_' . $kriteria->id, $nilaiAlternatifs->get($kriteria->id)->nilai ?? '') == '' ? 'selected' : '' }}>-- Pilih Nilai --</option>
                    <option value="2" {{ old('nilai_' . $kriteria->id, $nilaiAlternatifs->get($kriteria->id)->nilai ?? '') == '2' ? 'selected' : '' }}>Rendah</option>
                    <option value="6" {{ old('nilai_' . $kriteria->id, $nilaiAlternatifs->get($kriteria->id)->nilai ?? '') == '6' ? 'selected' : '' }}>Sedang</option>
                    <option value="9" {{ old('nilai_' . $kriteria->id, $nilaiAlternatifs->get($kriteria->id)->nilai ?? '') == '9' ? 'selected' : '' }}>Tinggi</option>
                </select>
                @error('nilai_' . $kriteria->id)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            @empty
            <div class="alert alert-warning text-white">Tidak ada kriteria yang terdaftar. Harap tambahkan kriteria terlebih dahulu di menu "Kelola Kriteria".</div>
            @endforelse

            <div class="mt-4">
                @if ($kriterias->count() > 0)
                    <button type="submit" class="btn btn-primary">Simpan Nilai</button>
                @endif

                <a href="{{ route('alternatif.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
