@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Kriteria</h1>
</div>

<form action="{{ route('kriteria.update', $kriterium->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label for="nama_kriteria" class="form-label">Nama Kriteria</label>
        @if (Auth::user()->isAdmin())
            <input type="text" class="form-control @error('nama_kriteria') is-invalid @enderror" id="nama_kriteria" name="nama_kriteria" value="{{ old('nama_kriteria', $kriterium->nama_kriteria) }}" required>
        @else
            <input type="text" class="form-control" id="nama_kriteria" name="nama_kriteria" value="{{ $kriterium->nama_kriteria }}" readonly>
            <input type="hidden" name="nama_kriteria" value="{{ $kriterium->nama_kriteria }}">
        @endif
        @error('nama_kriteria')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3">
        <label for="tipe" class="form-label">Tipe Kriteria</label>
        @if (Auth::user()->isAdmin())
            <select class="form-control @error('tipe') is-invalid @enderror" id="tipe" name="tipe" required>
                <option value="benefit" {{ old('tipe', $kriterium->tipe) == 'benefit' ? 'selected' : '' }}>Benefit</option>
                <option value="cost" {{ old('tipe', $kriterium->tipe) == 'cost' ? 'selected' : '' }}>Cost</option>
            </select>
        @else
            <input type="text" class="form-control" value="{{ ucfirst($kriterium->tipe) }}" readonly>
            <input type="hidden" name="tipe" value="{{ $kriterium->tipe }}">
        @endif
        @error('tipe')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3">
        <label for="bobot" class="form-label">Bobot (0-1)</label>
        <input type="number" step="0.01" class="form-control @error('bobot') is-invalid @enderror" id="bobot" name="bobot" value="{{ old('bobot', $kriterium->bobot) }}" required>
        @error('bobot')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <button type="submit" class="btn btn-primary">Update Kriteria</button>
    <a href="{{ route('kriteria.index') }}" class="btn btn-secondary">Batal</a>
</form>
@endsection
