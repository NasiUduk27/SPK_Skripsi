@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Tambah Kriteria</h1>
</div>

<form action="{{ route('kriteria.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="nama_kriteria" class="form-label">Nama Kriteria</label>
        <input type="text" class="form-control @error('nama_kriteria') is-invalid @enderror" id="nama_kriteria" name="nama_kriteria" value="{{ old('nama_kriteria') }}" required>
        @error('nama_kriteria')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3">
        <label for="tipe" class="form-label">Tipe Kriteria</label>
        <select class="form-control @error('tipe') is-invalid @enderror" id="tipe" name="tipe" required>
            <option value="benefit" {{ old('tipe') == 'benefit' ? 'selected' : '' }}>Benefit</option>
            <option value="cost" {{ old('tipe') == 'cost' ? 'selected' : '' }}>Cost</option>
        </select>
        @error('tipe')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3">
        <label for="bobot" class="form-label">Bobot (0-1)</label>
        <input type="number" step="0.01" class="form-control @error('bobot') is-invalid @enderror" id="bobot" name="bobot" value="{{ old('bobot') }}" required>
        @error('bobot')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <button type="submit" class="btn btn-primary">Simpan Kriteria</button>
    <a href="{{ route('kriteria.index') }}" class="btn btn-secondary">Batal</a>
</form>
@endsection