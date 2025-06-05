@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Alternatif</h1>
</div>

<form action="{{ route('alternatif.update', $alternatif->id) }}" method="POST">
    @csrf
    @method('PUT') 

    <div class="mb-3">
        <label for="nama_alternatif" class="form-label">Nama Alternatif</label>
        <input type="text" class="form-control @error('nama_alternatif') is-invalid @enderror" id="nama_alternatif" name="nama_alternatif" value="{{ old('nama_alternatif', $alternatif->nama_alternatif) }}" required>
        @error('nama_alternatif')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary">Update Alternatif</button>
    <a href="{{ route('alternatif.index') }}" class="btn btn-secondary">Batal</a>
</form>
@endsection
