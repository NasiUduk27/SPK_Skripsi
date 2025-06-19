@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard Sistem Pendukung Keputusan Vikor</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person-check fs-2 me-3 text-primary"></i>
                        <div>
                            <h5 class="card-title mb-0">Selamat Datang, {{ Auth::user()->name }}!</h5>
                            <p class="card-text text-muted">Anda berhasil login ke sistem.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (Auth::user()->isAdmin())
            {{-- Tampilan Admin --}}
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-people fs-2 me-3 text-warning"></i>
                            <div>
                                <h5 class="card-title mb-0">Total Pengguna</h5>
                                <p class="card-text fs-4 mb-0">{{ \App\Models\User::count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-stack fs-2 me-3 text-success"></i>
                            <div>
                                <h5 class="card-title mb-0">Total Kriteria</h5>
                                <p class="card-text fs-4 mb-0">{{ \App\Models\Kriteria::count() }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-top-0">
                        <a href="{{ route('kriteria.index') }}" class="btn btn-sm btn-outline-success">Kelola Kriteria</a>
                    </div>
                </div>
            </div>
        @else
            {{-- Tampilan User Biasa --}}
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-lightbulb fs-2 me-3 text-info"></i>
                            <div>
                                <h5 class="card-title mb-0">Alternatif Anda</h5>
                                <p class="card-text fs-4 mb-0">{{ Auth::user()->alternatifs()->count() }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-top-0">
                        <a href="{{ route('alternatif.index') }}" class="btn btn-sm btn-outline-info">Kelola Alternatif Anda</a>
                    </div>
                </div>
            </div>
             <div class="col-lg-4 col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-stack fs-2 me-3 text-success"></i>
                            <div>
                                <h5 class="card-title mb-0">Total Kriteria</h5>
                                <p class="card-text fs-4 mb-0">{{ \App\Models\Kriteria::count() }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-top-0">
                        <a href="{{ route('kriteria.index') }}" class="btn btn-sm btn-outline-success">Lihat Kriteria</a>
                    </div>
                </div>
            </div>
        @endif

        <div class="col-12 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                    <i class="bi bi-calculator fs-1 text-secondary mb-3"></i>
                    <h5 class="card-title">Siap Menghitung Solusi Terbaik?</h5>
                    <p class="card-text text-muted">Setelah semua kriteria dan alternatif diinput, Anda bisa melakukan perhitungan VIKOR.</p>
                    <a href="{{ route('vikor.hitung') }}" class="btn btn-primary btn-lg mt-3">
                        <i class="bi bi-arrow-right-circle me-2"></i>Mulai Perhitungan VIKOR
                    </a>
                </div>
            </div>
        </div>

        {{-- Card Info/Pesan --}}
        @if (session('status'))
            <div class="col-12 mb-4">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>

    </script>
    @endpush
    @endsection
