@extends('layouts.app')

@section('page_title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-body text-center p-5">
                <h1 class="font-weight-bolder">Sistem Pendukung Keputusan Penentuan Jenis Usaha</h1>
                <p class="lead">Temukan jenis usaha terbaik untuk Anda dengan metode VIKOR melalui 3 langkah mudah.</p>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-4 mb-lg-0 mb-4">
        <div class="card h-100">
            <div class="card-header text-center bg-gradient-primary text-white">
                <h3 class="card-title text-white mb-0"><span class="badge bg-white text-primary me-2">1</span> Kelola Kriteria</h3>
            </div>
            <div class="card-body d-flex flex-column text-center">
                <p class="text-sm">Di sini Anda bisa menambah, mengubah, atau menghapus faktor-faktor penentu keputusan. Misalnya: "Modal Awal", "Potensi Keuntungan", dll.</p>
                <div class="mt-auto">
                    <a href="{{ route('kriteria.index') }}" class="btn btn-primary w-100">
                        Mulai Langkah 1
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-lg-0 mb-4">
        <div class="card h-100">
            <div class="card-header text-center {{ $kriteriaCount > 0 ? 'bg-gradient-info' : 'bg-secondary' }} text-white">
                <h3 class="card-title text-white mb-0"><span class="badge bg-white {{ $kriteriaCount > 0 ? 'text-info' : 'text-secondary' }} me-2">2</span> Kelola Alternatif</h3>
            </div>
            <div class="card-body d-flex flex-column text-center">
                <p class="text-sm">Setelah kriteria siap, tambahkan pilihan-pilihan usaha yang ingin Anda bandingkan, lalu input nilainya untuk setiap kriteria.</p>
                <div class="mt-auto">
                    @if ($kriteriaCount > 0)
                        <a href="{{ route('alternatif.index') }}" class="btn btn-info w-100">
                            Mulai Langkah 2
                        </a>
                    @else
                        <button class="btn btn-secondary w-100" disabled>
                            Selesaikan Langkah 1 Dulu
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-lg-0 mb-4">
        <div class="card h-100">
            <div class="card-header text-center {{ $alternatifCount > 0 ? 'bg-gradient-success' : 'bg-secondary' }} text-white">
                <h3 class="card-title text-white mb-0"><span class="badge bg-white {{ $alternatifCount > 0 ? 'text-success' : 'text-secondary' }} me-2">3</span> Mulai Perhitungan</h3>
            </div>
            <div class="card-body d-flex flex-column text-center">
                <p class="text-sm">Jika semua data kriteria dan alternatif sudah terisi, Anda siap untuk melakukan perhitungan dan melihat hasilnya.</p>
                <div class="mt-auto">
                     @if ($alternatifCount > 0)
                        <a href="{{ route('vikor.pilih') }}" class="btn btn-success w-100">
                            Mulai Langkah 3
                        </a>
                    @else
                        <button class="btn btn-secondary w-100" disabled>
                            Selesaikan Langkah 2 Dulu
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
