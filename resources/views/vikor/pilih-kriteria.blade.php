@extends('layouts.app')

@section('page_title', 'Langkah 3: Tentukan Bobot & Hitung')

@section('content')

@if ($errors->any())
    <div class="alert alert-danger text-white shadow-sm">
        <h5 class="alert-heading">Terdapat Kesalahan Input:</h5>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success text-white">{{ session('success') }}</div>
@endif

<form action="{{ route('vikor.hitung') }}" method="POST" id="form-perhitungan">
    @csrf
    <div class="row">
        <div class="col-lg-7">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h4 class="font-weight-bolder">Kriteria yang Dipilih</h4>
                    <p class="mb-0">Berikut adalah kriteria yang akan digunakan dalam perhitungan.</p>
                </div>
                <div class="card-body pt-3">
                    <ul class="list-group">
                        @foreach ($kriterias as $kriteria)
                        <li class="list-group-item border-radius-lg">
                            <input type="hidden" name="kriteria_ids[]" value="{{ $kriteria->id }}">
                            {{ $kriteria->nama_kriteria }} ({{ ucfirst($kriteria->tipe) }})
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-header pb-0">
                    <h4 class="font-weight-bolder">Pilih Metode Pembobotan</h4>
                </div>
                <div class="card-body">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" name="metode_bobot" id="metode_bobot_sama" value="sama" checked>
                        <label class="form-check-label" for="metode_bobot_sama">
                            <strong class="d-block">Bobot Sama Rata</strong>
                            <span class="text-muted text-sm">Semua kriteria yang dipilih akan memiliki bobot yang sama.</span>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="metode_bobot" id="metode_bobot_prioritas" value="prioritas">
                        <label class="form-check-label" for="metode_bobot_prioritas">
                            <strong class="d-block">Bobot Berdasarkan Prioritas</strong>
                             <span class="text-muted text-sm">Anda akan menentukan urutan prioritas kriteria.</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5 mt-4 mt-lg-0">
            <div class="card" id="panel-prioritas" style="display: none;">
                <div class="card-header pb-0 bg-gradient-warning">
                    <h4 class="font-weight-bolder text-white">Urutkan Prioritas</h4>
                </div>
                <div class="card-body">
                    <p class="text-sm">Seret dan lepas (drag & drop) kriteria di bawah ini. Nomor 1 adalah yang paling penting.</p>
                    <ul id="prioritas-list" class="list-group">
                        @foreach ($kriterias as $kriteria)
                            <li class="list-group-item d-flex justify-content-between align-items-center" data-id="{{ $kriteria->id }}" style="cursor: grab;">
                                <span><i class="fas fa-bars me-3 text-muted"></i>{{ $kriteria->nama_kriteria }}</span>
                                <span class="badge bg-primary rounded-pill"></span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-calculator me-2"></i> Lihat Hasil Perhitungan
                </button>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const metodeBobotRadios = document.querySelectorAll('input[name="metode_bobot"]');
    const panelPrioritas = document.getElementById('panel-prioritas');
    const prioritasList = document.getElementById('prioritas-list');
    let sortable;

    function updateRanks() {
        const items = prioritasList.querySelectorAll('li');
        items.forEach((item, index) => {
            item.querySelector('.badge').textContent = index + 1;
            const oldInput = item.querySelector('input[type="hidden"]');
            if(oldInput) oldInput.remove();
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = `prioritas[${item.dataset.id}]`;
            hiddenInput.value = index + 1;
            item.appendChild(hiddenInput);
        });
    }

    updateRanks();

    metodeBobotRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            if (radio.value === 'prioritas') {
                panelPrioritas.style.display = 'block';
                if (!sortable) {
                    sortable = new Sortable(prioritasList, {
                        animation: 150,
                        ghostClass: 'bg-light',
                        onEnd: updateRanks
                    });
                }
            } else {
                panelPrioritas.style.display = 'none';
            }
        });
    });
});
</script>
@endpush
@endsection
