@extends('layouts.app')

@section('page_title', 'Langkah 2: Kelola Alternatif & Nilai')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header pb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="font-weight-bolder">Daftar Alternatif & Input Nilai</h4>
                    <a href="{{ route('alternatif.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Tambah Alternatif Baru
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success text-white">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger text-white">{{ session('error') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger text-white">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="alert alert-info text-white">
                    <i class="fas fa-info-circle me-2"></i>
                    Anda sedang bekerja dengan <strong>{{ $kriterias->count() }} kriteria</strong> yang dipilih. Silakan isi semua nilai di bawah ini sebelum melanjutkan.
                </div>

                <form action="{{ route('alternatif.simpanDanLanjutkan') }}" method="POST">
                    @csrf

                    @if($alternatifs->isNotEmpty())
                        <div class="row">
                            @foreach ($alternatifs as $alternatif)
                            <div class="col-lg-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">{{ $loop->iteration }}. {{ $alternatif->nama_alternatif }}</h5>
                                        <div>
                                            <a href="{{ route('alternatif.edit', $alternatif->id) }}" class="btn btn-warning btn-sm mb-0" title="Edit Nama">
                                                <i class="fas fa-edit"></i> Edit Nama
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm mb-0"
                                                    onclick="if(confirm('Anda yakin ingin menghapus alternatif ini?')) { document.getElementById('delete-form-{{ $alternatif->id }}').submit(); }"
                                                    title="Hapus">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body pt-0">
                                        @foreach ($kriterias as $kriteria)
                                            @php
                                                $currentValue = $alternatif->nilaiAlternatifs->where('kriteria_id', $kriteria->id)->first()->nilai ?? '';
                                            @endphp
                                            <div class="mb-2">
                                                <label class="form-label">{{ $kriteria->nama_kriteria }}</label>
                                                <select class="form-control" name="nilai[{{ $alternatif->id }}][{{ $kriteria->id }}]">
                                                    <option value="" {{ $currentValue == '' ? 'selected' : '' }}>- Pilih Nilai -</option>
                                                    <option value="2" {{ $currentValue == '2' ? 'selected' : '' }}>Rendah</option>
                                                    <option value="6" {{ $currentValue == '6' ? 'selected' : '' }}>Sedang</option>
                                                    <option value="9" {{ $currentValue == '9' ? 'selected' : '' }}>Tinggi</option>
                                                </select>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-save me-2"></i> Simpan Nilai & Lanjutkan ke Perhitungan
                            </button>
                        </div>
                    @else
                        <div class="alert alert-light text-center">
                            Belum ada alternatif. Silakan klik tombol "Tambah Alternatif Baru" untuk memulai.
                        </div>
                    @endif
                </form>

                @foreach ($alternatifs as $alternatif)
                    <form id="delete-form-{{ $alternatif->id }}" action="{{ route('alternatif.destroy', $alternatif->id) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                @endforeach

            </div>
        </div>
    </div>
</div>
@endsection
