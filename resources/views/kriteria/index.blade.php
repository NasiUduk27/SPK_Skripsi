@extends('layouts.app')

@section('page_title', 'Langkah 1: Tentukan Kriteria')

@section('content')
<form action="{{ route('kriteria.prosesPemilihan') }}" method="POST">
    @csrf
    <div class="card">
        <div class="card-header pb-0">
            <h4 class="font-weight-bolder">Tentukan Kriteria</h4>
            <p class="mb-0">Centang kriteria yang ingin Anda gunakan dalam perhitungan (minimal 2).</p>
        </div>
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger text-white">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger text-white">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="list-group">
                @foreach ($kriterias as $kriteria)
                <label class="list-group-item border-radius-lg">
                    <input class="form-check-input me-2" type="checkbox" name="kriteria_ids[]" value="{{ $kriteria->id }}">
                    {{ $kriteria->nama_kriteria }} ({{ ucfirst($kriteria->tipe) }})
                </label>
                @endforeach
            </div>
            
            <div class="mt-4 pt-3 border-top">
                <h6 class="font-weight-bolder">Catatan:</h6>
                <p class="text-sm text-muted mb-1">
                    <strong class="text-success">Benefit (Keuntungan):</strong> Berarti untuk kriteria ini, semakin TINGGI nilainya maka akan semakin BAGUS.
                </p>
                <p class="text-sm text-muted mb-0">
                    <strong class="text-danger">Cost (Biaya):</strong> Berarti untuk kriteria ini, semakin RENDAH nilainya maka akan semakin BAGUS.
                </p>
            </div>

        </div>
        <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary">
                Simpan Pilihan & Lanjutkan ke Langkah 2 <i class="fas fa-arrow-right ms-2"></i>
            </button>
        </div>
    </div>
</form>
@endsection
