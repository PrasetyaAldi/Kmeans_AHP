@extends('layouts.app')

@section('title', 'AHP')

@section('header')
    <div class="row">
        <div class="col-12">
            <h4 class="text-start">Perbandingan</h4>
        </div>
    </div>
@endsection

@section('content')
    <div class="card shadow">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <h3 class="card-title">Bobot Kriteria</h3>
                @if (!empty($weight_criteria->toArray()))
                    <form action="{{ route('ahps.reset') }}" method="POST">
                        @csrf
                        <button class="btn btn-warning" style="background-color: var(--bs-warning-bg-subtle)"
                            type="submit"><i class="fa-solid fa-gear"></i> Hitung Ulang</button>
                    </form>
                @endif
            </div>
        </div>
        <div class="card-body">
            @if (!empty($weight_criteria->toArray()))
                {{-- <img class="img-fluid mx-auto rounded d-block" style="width: 30%" src="{{ asset('assets/img/done.png') }}"
                    alt="done.png">
                <h4 class="card-title text-center">Bobot Kriteria sudah di hitung</h4> --}}
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Kriteria</th>
                            <th>Bobot</th>
                            <th>Eigen Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($weight_criteria as $item)
                            <tr>
                                <td>{{ $item->criteria->name }}</td>
                                <td>{{ $item->bobot }}</td>
                                <td>{{ $item->eigen_value }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div style="width:100%;overflow-x:scroll">
                    <table class="table">
                        <thead>
                            <tr>
                                <th></th>
                                @foreach ($data as $item)
                                    <th>{{ $item->name }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <form action="{{ route('ahps.store') }}" method="POST" id="weightCriteriaForm">
                                @csrf
                                @foreach ($data as $key1 => $item)
                                    <tr>
                                        <td>
                                            {{ $item->name }}
                                        </td>
                                        @foreach ($data as $key2 => $item2)
                                            <td>
                                                @if ($key1 == $key2)
                                                    <input type="number" class="form-control"
                                                        name="data[{{ $key1 }}][{{ $key2 }}]"
                                                        id="data[{{ $key1 }}][{{ $key2 }}]"
                                                        value="{{ old('data[' . $key1 . '][' . $key2 . ']') ?? 1 }}"
                                                        min="1" max="9" readonly
                                                        style="background-color: gray">
                                                @else
                                                    <input type="number" class="form-control"
                                                        name="data[{{ $key1 }}][{{ $key2 }}]"
                                                        id="data[{{ $key1 }}][{{ $key2 }}]"
                                                        value="{{ old('data[' . $key1 . '][' . $key2 . ']') ?? 1 }}"
                                                        min="1" max="9">
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </form>
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        @if (empty($weight_criteria->toArray()))
            <div class="card-footer d-flex justify-content-end">
                <button class="btn btn-primary" onclick="submitForm()">
                    <i class="fa-solid fa-floppy-disk"></i> Submit
                </button>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.querySelectorAll('input[type="number"]')
            input.forEach((item) => {
                item.addEventListener('change', function() {
                    const id = this.id.split(/[\[\]]/).filter(Boolean)
                    const value = this.value
                    if (value > 9 || value < 1) {
                        alert('Nilai Maksimal 9 dan Minimal 0.0')
                        this.value = 1
                        value = 1
                    }
                    const input2 = document.getElementById(`data[${id[2]}][${id[1]}]`)
                    input2.value = 1 / value
                })
            })
        })

        /**
         * submit form
         */
        const submitForm = () => {
            const form = document.getElementById('weightCriteriaForm')
            form.submit()
        }
    </script>

@endsection
