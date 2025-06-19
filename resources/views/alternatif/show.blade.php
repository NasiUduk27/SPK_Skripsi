@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Detail Alternatif: {{ $alternatif->nama_alternatif }}</h1>
</div>

<div class="card mb-4">
    <div class="card-header">
        Informasi Alternatif
    </div>
    <div class="card-body">
        <p><strong>Nama Alternatif:</strong> {{ $alternatif->nama_alternatif }}</p>
        <a href="{{ route('alternatif.edit', $alternatif->id) }}" class="btn btn-warning btn-sm">Edit Alternatif</a>
        <a href="{{ route('alternatif.index') }}" class="btn btn-secondary btn-sm">Kembali ke Daftar Alternatif</a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        Nilai Kriteria
    </div>
    <div class="card-body">
        <a href="{{ route('alternatif.inputNilai', $alternatif->id) }}" class="btn btn-primary mb-3">Input / Edit Nilai</a>

        @if($kriterias->isEmpty())
            <div class="alert alert-warning">Tidak ada kriteria yang terdaftar. Harap tambahkan kriteria terlebih dahulu.</div>
        @else
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Kriteria</th>
                        <th>Nilai Disimpan (Standar)</th>
                        <th>Kategori</th>
                        <th>Range Asal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kriterias as $kriteria)
                    <tr>
                        <td>{{ $kriteria->nama_kriteria }} ({{ ucfirst($kriteria->tipe) }})</td>
                        <td>
                            @php
                                $nilaiObj = $nilaiAlternatifs->get($kriteria->id);
                            @endphp

                            @if ($nilaiObj)
                                {{ $nilaiObj->nilai }}
                            @else
                                Belum Diisi
                            @endif
                        </td>
                        <td>
                            @if ($nilaiObj)
                                {{ $nilaiObj->kategori_nilai }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if ($nilaiObj)
                                {{ $nilaiObj->range_angka }} 
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4">Tidak ada nilai yang diinput untuk alternatif ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
