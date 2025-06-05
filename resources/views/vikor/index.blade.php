@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Hasil Perhitungan VIKOR</h1>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Alternatif</th>
                <th>S</th>
                <th>R</th>
                <th>Q</th>
                <th>Ranking</th>
            </tr>
        </thead>
        <tbody>
            @php $rank = 1; @endphp
            @foreach ($Q as $id => $q)
                <tr>
                    <td>{{ $alternatifs->find($id)->nama }}</td>
                    <td>{{ round($S[$id], 4) }}</td>
                    <td>{{ round($R[$id], 4) }}</td>
                    <td>{{ round($q, 4) }}</td>
                    <td>{{ $rank++ }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection