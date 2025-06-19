@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <h1 class="h2">Hasil Perhitungan VIKOR</h1>
    </div>

    @if (isset($error))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ $error }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @else
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- 1. Data Input (sama seperti sebelumnya) --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title mb-0"><i class="fas fa-clipboard-list me-2"></i>Data Input</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <h4 class="mb-3 text-primary">Kriteria</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover table-sm">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama</th>
                                        <th>Tipe</th>
                                        <th>Bobot</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kriterias as $kriteria)
                                    <tr>
                                        <td>{{ $kriteria->id }}</td>
                                        <td>{{ $kriteria->nama_kriteria }}</td>
                                        <td>{{ ucfirst($kriteria->tipe) }}</td>
                                        <td>{{ $kriteria->bobot }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h4 class="mb-3 text-primary">Alternatif & Nilai</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover table-sm">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Alternatif</th>
                                        @foreach ($kriterias as $kriteria)
                                            <th>{{ $kriteria->nama_kriteria }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($alternatifs as $alternatif)
                                    <tr>
                                        <td>{{ $alternatif->nama_alternatif }}</td>
                                        {{-- Gunakan accessor untuk menampilkan nilai dan kategori jika diperlukan --}}
                                        @foreach ($kriterias as $kriteria)
                                            <td>
                                                @php
                                                    $nilaiObj = $alternatif->getNilaiByKriteria($kriteria);
                                                @endphp
                                                @if ($nilaiObj)
                                                    {{ $nilaiObj->nilai }} ({{ $nilaiObj->kategori_nilai }})
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Nilai F* dan F- (sama seperti sebelumnya) --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h3 class="card-title mb-0"><i class="fas fa-calculator me-2"></i>Nilai F* dan F-</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>Kriteria</th>
                                <th>F* (Max Benefit / Min Cost)</th>
                                <th>F- (Min Benefit / Max Cost)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($kriterias as $kriteria)
                            <tr>
                                <td>{{ $kriteria->nama_kriteria }}</td>
                                <td>{{ number_format($fStar[$kriteria->id], 4) }}</td>
                                <td>{{ number_format($fMinus[$kriteria->id], 4) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- 3. Nilai Si dan Ri (sama seperti sebelumnya) --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h3 class="card-title mb-0"><i class="fas fa-balance-scale me-2"></i>Nilai Si dan Ri</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>Alternatif</th>
                                <th>Si</th>
                                <th>Ri</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($alternatifs as $alternatif)
                            <tr>
                                <td>{{ $alternatif->nama_alternatif }}</td>
                                <td>{{ number_format($Si[$alternatif->id], 4) }}</td>
                                <td>{{ number_format($Ri[$alternatif->id], 4) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- 4. Nilai Qi dan Ranking (sama seperti sebelumnya) --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-warning text-dark">
                <h3 class="card-title mb-0"><i class="fas fa-sort-numeric-down-alt me-2"></i>Nilai Qi dan Ranking</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>Ranking</th>
                                <th>Alternatif</th>
                                <th>Qi</th>
                                <th>Si</th>
                                <th>Ri</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ranking as $key => $rank)
                            <tr>
                                <td><span class="badge bg-primary rounded-pill">{{ $key + 1 }}</span></td>
                                <td>{{ $rank['alternatif'] }}</td>
                                <td>{{ number_format($rank['Qi'], 4) }}</td>
                                <td>{{ number_format($rank['Si'], 4) }}</td>
                                <td>{{ number_format($rank['Ri'], 4) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- 5. Solusi Kompromi Terbaik (Perubahan di sini) --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h3 class="card-title mb-0"><i class="fas fa-trophy me-2"></i>Solusi Kompromi Terbaik</h3>
            </div>
            <div class="card-body">
                @if ($kandidatTerbaik)
                    <p class="lead">Alternatif terbaik (Q terkecil): <strong class="text-primary">{{ $kandidatTerbaik['alternatif'] ?? 'Belum terdefinisi' }}</strong> dengan nilai Qi = <strong class="text-success">{{ number_format($kandidatTerbaik['Qi'] ?? 0, 4) }}</strong></p>

                    <p class="mb-2">Status Solusi: <span class="badge bg-secondary">{{ $kandidatTerbaik['status'] ?? 'Menunggu perhitungan' }}</span></p>

                    @if (isset($kandidatTerbaik['set_solusi_kompromi']) && count($kandidatTerbaik['set_solusi_kompromi']) > 1)
                        <p class="mb-2">Set Solusi Kompromi:
                            @foreach ($kandidatTerbaik['set_solusi_kompromi'] as $solusi)
                                <span class="badge bg-info text-dark">{{ $solusi }}</span>@if (!$loop->last),@endif
                            @endforeach
                        </p>
                    @endif

                    @if (isset($DQ))
                        <p class="mb-0">Nilai DQ (Threshold untuk Kondisi 1): <strong class="text-info">{{ number_format($DQ, 4) }}</strong></p>
                    @endif
                @else
                    <div class="alert alert-info mb-0" role="alert">
                        Tidak ada alternatif yang dapat dihitung. Pastikan data kriteria dan alternatif sudah lengkap.
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
