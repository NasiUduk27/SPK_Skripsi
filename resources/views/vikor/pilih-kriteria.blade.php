@extends('layouts.app')

@section('page_title', 'Mulai Perhitungan')

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

<form action="{{ route('vikor.hitung') }}" method="POST" id="form-perhitungan">
    @csrf
    <div class="row">
        <div class="col-lg-7">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h4 class="font-weight-bolder">Langkah 1: Pilih Kriteria</h4>
                    <p class="mb-0">Centang kriteria yang relevan untuk analisis Anda (minimal 2).</p>
                </div>
                <div class="card-body pt-3">
                    <div id="kriteria-list" class="list-group">
                        @forelse ($kriterias as $kriteria)
                        <label class="list-group-item border-radius-lg">
                            <input class="form-check-input me-2" type="checkbox" name="kriteria_ids[]" value="{{ $kriteria->id }}" data-nama="{{ $kriteria->nama_kriteria }}">
                            {{ $kriteria->nama_kriteria }} ({{ ucfirst($kriteria->tipe) }})
                        </label>
                        @empty
                        <div class="alert alert-warning text-white">Tidak ada kriteria. Silakan <a href="{{ route('kriteria.create') }}" class="alert-link text-white">tambahkan kriteria</a> terlebih dahulu.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header pb-0">
                    <h4 class="font-weight-bolder">Langkah 2: Pilih Metode Pembobotan</h4>
                </div>
                <div class="card-body">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" name="metode_bobot" id="metode_bobot_sama" value="sama" checked>
                        <label class="form-check-label" for="metode_bobot_sama">
                            <strong class="d-block">Bobot Sama Rata</strong>
                            <span class="text-muted text-sm">Semua kriteria yang dipilih akan memiliki bobot yang sama (dibagi rata).</span>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="metode_bobot" id="metode_bobot_prioritas" value="prioritas">
                        <label class="form-check-label" for="metode_bobot_prioritas">
                            <strong class="d-block">Bobot Berdasarkan Prioritas</strong>
                             <span class="text-muted text-sm">Anda akan menentukan urutan prioritas kriteria. Kriteria paling penting berada di urutan teratas.</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5 mt-4 mt-lg-0">
            <div class="card" id="panel-prioritas" style="display: none;">
                <div class="card-header pb-0 bg-gradient-warning">
                    <h4 class="font-weight-bolder text-white">Langkah 3: Urutkan Prioritas</h4>
                </div>
                <div class="card-body">
                    <p class="text-sm">Seret dan lepas (drag & drop) kriteria di bawah ini. Nomor 1 adalah yang paling penting.</p>
                    <ul id="prioritas-list" class="list-group">
                    </ul>
                    <div id="prioritas-placeholder" class="alert alert-info text-white mt-3">Pilih kriteria di sebelah kiri untuk mulai mengurutkan.</div>
                </div>
            </div>

            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-calculator me-2"></i> Hitung Hasil VIKOR
                </button>
            </div>
            <div class="card mt-4">
                <div class="card-header pb-0">
                    <h5 class="font-weight-bolder"><i class="fas fa-info-circle text-primary me-2"></i>Petunjuk Pengisian</h5>
                </div>
                <div class="card-body pt-2">
                    <p class="text-sm">
                        Halo! Bingung mulai dari mana? Yuk, ikuti langkah-langkah mudah ini untuk mendapatkan hasil terbaik:
                    </p>
                    <ol class="list-group list-group-numbered">
                        <li class="list-group-item border-0 ps-0">
                            <strong class="d-block">Siapkan Data Anda</strong>
                            <span class="text-xs">Pastikan semua data sudah lengkap. Buka menu <a href="{{ route('kriteria.index') }}">"Kelola Kriteria"</a> untuk menambah/mengedit faktor penentu, dan menu <a href="{{ route('alternatif.index') }}">"Kelola Alternatif"</a> untuk menambah/mengedit pilihan usaha dan mengisi nilainya.</span>
                        </li>
                        <li class="list-group-item border-0 ps-0">
                            <strong class="d-block">Pilih Kriteria (Langkah 1)</strong>
                            <span class="text-xs">Di halaman ini, centang hanya kriteria yang ingin Anda gunakan untuk perhitungan saat ini.</span>
                        </li>
                        <li class="list-group-item border-0 ps-0">
                            <strong class="d-block">Tentukan Bobot (Langkah 2 & 3)</strong>
                            <span class="text-xs">Pilih "Sama Rata" jika semua kriteria dianggap sama penting. Pilih "Prioritas" jika ada kriteria yang lebih penting, lalu seret dan urutkan pada panel yang muncul.</span>
                        </li>
                         <li class="list-group-item border-0 ps-0">
                            <strong class="d-block">Lihat Hasilnya!</strong>
                            <span class="text-xs">Setelah semua siap, klik tombol "Hitung Hasil VIKOR" untuk melihat peringkat alternatif usaha terbaik berdasarkan pilihan Anda.</span>
                        </li>
                    </ol>
                </div>
            </div>

        </div>
    </div>
</form>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const kriteriaCheckboxes = document.querySelectorAll('#kriteria-list input[type="checkbox"]');
    const metodeBobotRadios = document.querySelectorAll('input[name="metode_bobot"]');
    const panelPrioritas = document.getElementById('panel-prioritas');
    const prioritasList = document.getElementById('prioritas-list');
    const prioritasPlaceholder = document.getElementById('prioritas-placeholder');
    let sortable;

    function updatePrioritasList() {
        const currentOrder = Array.from(prioritasList.querySelectorAll('li')).map(li => li.dataset.id);
        prioritasList.innerHTML = '';
        const selectedKriteria = Array.from(kriteriaCheckboxes).filter(cb => cb.checked);

        selectedKriteria.sort((a, b) => {
            const indexA = currentOrder.indexOf(a.value);
            const indexB = currentOrder.indexOf(b.value);
            if (indexA === -1 && indexB === -1) return 0;
            if (indexA === -1) return 1;
            if (indexB === -1) return -1;
            return indexA - indexB;
        });

        if (selectedKriteria.length > 0) {
            prioritasPlaceholder.style.display = 'none';
            selectedKriteria.forEach(cb => {
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                li.dataset.id = cb.value;
                li.style.cursor = 'grab';
                li.innerHTML = `<span><i class="fas fa-bars me-3 text-muted"></i>${cb.dataset.nama}</span><span class="badge bg-primary rounded-pill"></span>`;
                prioritasList.appendChild(li);
            });
            updateRanks();
        } else {
            prioritasPlaceholder.style.display = 'block';
        }
    }

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

    kriteriaCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            if (document.getElementById('metode_bobot_prioritas').checked) {
                updatePrioritasList();
            }
        });
    });

    metodeBobotRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            if (radio.value === 'prioritas') {
                panelPrioritas.style.display = 'block';
                updatePrioritasList();
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
