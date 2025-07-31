@extends('layouts.app')

@section('page_title', 'Edit Kriteria')

@section('content')
<div class="card">
    <div class="card-header pb-0">
        <h4 class="font-weight-bolder">Edit Kriteria</h4>
        <p class="mb-0">Ubah detail kriteria di bawah ini.</p>
    </div>
    <div class="card-body">
        <form action="{{ route('kriteria.update', $kriterium->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="nama_kriteria" class="form-label">Nama Kriteria</label>
                <input type="text" class="form-control @error('nama_kriteria') is-invalid @enderror" id="nama_kriteria" name="nama_kriteria" value="{{ old('nama_kriteria', $kriterium->nama_kriteria) }}" required>
                @error('nama_kriteria')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="tipe" class="form-label">Tipe Kriteria</label>
                <select class="form-control @error('tipe') is-invalid @enderror" id="tipe" name="tipe" required>
                    <option value="benefit" {{ old('tipe', $kriterium->tipe) == 'benefit' ? 'selected' : '' }}>Benefit</option>
                    <option value="cost" {{ old('tipe', $kriterium->tipe) == 'cost' ? 'selected' : '' }}>Cost</option>
                </select>
                @error('tipe')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="bobot" class="form-label">Bobot Relatif</label>
                <input type="number" step="any" class="form-control @error('bobot') is-invalid @enderror" id="bobot" name="bobot" value="{{ old('bobot', $kriterium->bobot) }}" required>
                @error('bobot')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Update Kriteria</button>
                <a href="{{ route('kriteria.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
