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
            </div>
        </div>
        <div class="card-body">
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
        </div>
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
