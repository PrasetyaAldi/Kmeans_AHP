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
                {{-- @if (!empty($weight_criteria->toArray()))
                    <form action="{{ route('ahps.reset') }}" method="POST">
                        @csrf
                        <button class="btn btn-warning" style="background-color: var(--bs-warning-bg-subtle)"
                            type="submit"><i class="fa-solid fa-gear"></i> Hitung Ulang</button>
                    </form>
                @endif --}}
            </div>
        </div>
        <div class="card-body">
            @if (!empty($weight_criteria->toArray()))
                <div class="alert alert-primary" role="alert">
                    Nilai CR : {{ $weight_criteria->first()->cr }}
                </div>
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
                    <form action="{{ route('ahps.store') }}" method="POST" id="weightCriteriaForm">
                        @csrf
                        <table class="table">
                            <thead>
                                <tr>
                                    <th></th>
                                    @foreach ($data as $item)
                                        <th>{{ $item->name }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody id="dynamic-tbody">
                                <td colspan="{{ count($data) + 1 }}">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <span>Menunggu Render Data Selesai...</span>
                                </td>
                            </tbody>
                        </table>
                    </form>
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
            @if (empty($weight_criteria->toArray()))
                const xhr = new XMLHttpRequest()
                xhr.open('GET', '{{ route('ahps.data-criteria') }}')

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        var data = JSON.parse(xhr.responseText)
                        const tbody = document.getElementById('dynamic-tbody')
                        tbody.innerHTML = ''

                        data.forEach((item, key) => {
                            const newRow = document.createElement('tr')
                            newRow.innerHTML = `<th class="headcol">${item.name}</th>`
                            data.forEach((item2, key2) => {
                                const isReadOnly = key == key2 ? 'readonly' : ''
                                const bgColor = key == key2 ? 'background-color: gray' : ''
                                const value = key == key2 ? 1 : Math.floor(Math.random() * 9) +
                                    1

                                newRow.innerHTML += `<td>
                                    <input type="number" class="form-control" 
                                    name="data[${key}][${key2}]" id="data[${key}][${key2}]"
                                    data-col="${key2}" data-row="${key}" value="${value}" 
                                    min="1" max="9" ${isReadOnly} style="${bgColor}">
                                </td>`
                            })
                            tbody.appendChild(newRow)
                        })
                        const input = document.querySelectorAll('input[type="number"]')
                        input.forEach((item) => {
                            item.addEventListener('change', function() {
                                const id = this.id.split(/[\[\]]/).filter(Boolean)
                                const value = this.value
                                if (value > 9 || value < 1) {
                                    alert('Nilai Maksimal 9 dan Minimal 0')
                                    this.value = 1
                                    value = 1
                                }
                                const input2 = document.getElementById(
                                    `data[${id[2]}][${id[1]}]`)
                                input2.value = 1 / value
                            })
                            const col = item.getAttribute('data-col')
                            const row = item.getAttribute('data-row')
                            if (col != row) {
                                // membuat nilai inputan berpasangan berubah dengan nilai awal di input
                                const input2 = document.getElementById(
                                    `data[${col}][${row}]`)
                                input2.value = 1 / item.value
                            }
                        })
                    } else {
                        console.error('Request failed.  Returned status of ' + xhr.status)
                    }
                }
                xhr.send()
            @endif
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
