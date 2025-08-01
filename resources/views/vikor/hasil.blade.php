@extends('layouts.app')

@section('page_title', 'Hasil Perhitungan')

@section('content')
<div class="container-fluid py-4">
   <div class="container-fluid py-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <h1 class="h2">Hasil Perhitungan VIKOR</h1>
        <a href="{{ route('vikor.pilih') }}" class="btn btn-outline-secondary">
            <i class="fas fa-redo me-2"></i> Ulangi Perhitungan
        </a>
    </div>

    @if (isset($error))
        <div class="alert alert-danger text-white shadow-sm" role="alert">
            <h4 class="alert-heading">Terjadi Masalah!</h4>
            <p>{{ $error }}</p>
        </div>
    @else
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <h3 class="card-title mb-0"></i>1. Data Input yang Digunakan</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-5 mb-4 mb-lg-0">
                        <h5 class="text-center">Kriteria & Bobot Ternormalisasi</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover table-sm text-center">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Nama Kriteria</th>
                                        <th>Tipe</th>
                                        <th>Bobot Digunakan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kriterias as $kriteria)
                                    <tr>
                                        <td>{{ $kriteria->nama_kriteria }}</td>
                                        <td>{{ ucfirst($kriteria->tipe) }}</td>
                                        <td>{{ number_format($kriteria->bobot_normalisasi, 4) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="fw-bold">
                                    <tr>
                                        <td colspan="2" class="text-end">Total Bobot:</td>
                                        <td>{{ number_format($kriterias->sum('bobot_normalisasi'), 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <h5 class="text-center">Matriks Keputusan (Nilai Alternatif)</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover table-sm text-center">
                                <thead class="table-primary">
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
                                        <td><strong>{{ $alternatif->nama_alternatif }}</strong></td>
                                        @foreach ($kriterias as $kriteria)
                                            <td>{{ $alternatif->getNilaiByKriteria($kriteria)->nilai ?? '-' }}</td>
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
        <div class="card shadow-sm mb-4">
             <div class="card-header bg-dark text-white">
                <h3 class="card-title mb-0">2. Nilai Ideal Positif (F*) & Negatif (F-)</h3>
            </div>
            <div class="card-body">
                 <div class="table-responsive">
                    <table class="table table-bordered table-sm text-center">
                        <thead class="table-info">
                            <tr>
                                <th>Kriteria</th>
                                @foreach ($kriterias as $kriteria)
                                    <th>{{ $kriteria->nama_kriteria }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>F* (Terbaik)</strong></td>
                                @foreach ($kriterias as $kriteria)
                                    <td>{{ number_format($fStar[$kriteria->id], 4) }}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <td><strong>F- (Terburuk)</strong></td>
                                @foreach ($kriterias as $kriteria)
                                    <td>{{ number_format($fMinus[$kriteria->id], 4) }}</td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card shadow-sm mb-4">
             <div class="card-header bg-dark text-white">
                <h3 class="card-title mb-0"></i>3. Nilai Utility (S) & Regret (R)</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-sm">
                        <thead class="table-warning">
                            <tr>
                                <th>Alternatif</th>
                                <th>Nilai S (Utility)</th>
                                <th>Nilai R (Regret)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($alternatifs as $alternatif)
                            <tr>
                                <td>{{ $alternatif->nama_alternatif }}</td>
                                <td>{{ number_format($Si[$alternatif->id], 6) }}</td>
                                <td>{{ number_format($Ri[$alternatif->id], 6) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <h3 class="card-title mb-0"></i>4. Perankingan Indeks VIKOR (Q)</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-sm">
                        <thead class="table-success">
                            <tr>
                                <th>Ranking</th>
                                <th>Alternatif</th>
                                <th>Nilai Q</th>
                                <th>Nilai S</th>
                                <th>Nilai R</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ranking as $key => $rank_item)
                            <tr class="{{ $key == 0 ? 'table-primary fw-bold' : '' }}">
                                <td><span class="badge bg-dark rounded-pill fs-6">{{ $key + 1 }}</span></td>
                                <td>{{ $rank_item['alternatif'] }}</td>
                                <td>{{ number_format($rank_item['Qi'], 6) }}</td>
                                <td>{{ number_format($rank_item['Si'], 6) }}</td>
                                <td>{{ number_format($rank_item['Ri'], 6) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title mb-0"></i>5. Solusi Kompromi</h3>
            </div>
            <div class="card-body">
                @if ($kandidatTerbaik)
                    <div class="alert alert-success text-center">
                        <h4 class="alert-heading">🏆 Alternatif Terbaik 🏆</h4>
                        <p class="lead mb-0">
                            <strong>{{ $kandidatTerbaik['alternatif'] }}</strong> dengan nilai Qi = <strong>{{ number_format($kandidatTerbaik['Qi'], 6) }}</strong>
                        </p>
                    </div>

                    <h5 class="mt-4">Analisis Kondisi Solusi Kompromi:</h5>
                    <ul>
                        <li>Nilai DQ (Threshold): <strong>{{ number_format($DQ, 6) }}</strong></li>
                        <li>Status: <span class="badge bg-info text-dark fs-6">{{ $kandidatTerbaik['status'] }}</span></li>
                    </ul>

                    @if (isset($kandidatTerbaik['set_solusi_kompromi']))
                        <h5 class="mt-3">Set Solusi Kompromi:</h5>
                        <p>
                            @foreach ($kandidatTerbaik['set_solusi_kompromi'] as $solusi)
                                <span class="badge bg-secondary fs-6 me-1">{{ $solusi }}</span>
                            @endforeach
                        </p>
                    @endif
                @else
                    <div class="alert alert-warning mb-0">
                        Tidak ada alternatif yang dapat dihitung atau diranking.
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
