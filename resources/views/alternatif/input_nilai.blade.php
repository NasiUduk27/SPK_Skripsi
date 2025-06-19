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
        <select class="form-select @error('nilai_' . $kriteria->id) is-invalid @enderror" id="nilai_{{ $kriteria->id }}" name="nilai_{{ $kriteria->id }}" required>
            <option value="">Pilih Nilai</option>
            {{--
                Nilai (value) di sini adalah nilai numerik standar yang akan disimpan ke database
                sesuai dengan kategori yang dipilih.
                Contoh:
                Rendah (1-4) -> value="2"
                Sedang (5-7) -> value="6"
                Tinggi (8-10) -> value="9"
            --}}
            <option value="2" {{ old('nilai_' . $kriteria->id, $nilaiAlternatifs->get($kriteria->id)->nilai ?? '') == '2' ? 'selected' : '' }}>Rendah (1-4)</option>
            <option value="6" {{ old('nilai_' . $kriteria->id, $nilaiAlternatifs->get($kriteria->id)->nilai ?? '') == '6' ? 'selected' : '' }}>Sedang (5-7)</option>
            <option value="9" {{ old('nilai_' . $kriteria->id, $nilaiAlternatifs->get($kriteria->id)->nilai ?? '') == '9' ? 'selected' : '' }}>Tinggi (8-10)</option>
        </select>
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
