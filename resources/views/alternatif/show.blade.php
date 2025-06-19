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
                        <th>Nilai Disimpan (Standar)</th> {{-- Nilai numerik standar yang tersimpan (2, 6, atau 9) --}}
                        <th>Kategori</th> {{-- Kategori teks (Rendah, Sedang, Tinggi) --}}
                        <th>Range Asal</th> {{-- Rentang angka asli (1-4, 5-7, 8-10) --}}
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
                                {{ $nilaiObj->nilai }} {{-- Menampilkan nilai numerik standar --}}
                            @else
                                Belum Diisi
                            @endif
                        </td>
                        <td>
                            @if ($nilaiObj)
                                {{ $nilaiObj->kategori_nilai }} {{-- Menampilkan kategori dari accessor --}}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if ($nilaiObj)
                                {{ $nilaiObj->range_angka }} {{-- Menampilkan rentang angka dari accessor --}}
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
