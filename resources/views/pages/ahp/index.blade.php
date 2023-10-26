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
                                @foreach ($data as $key => $item)
                                    <tr>
                                        <th class="headcol">{{ $item->name }}</th>
                                        @foreach ($input[$key] as $key2 => $inputItem)
                                            <td>
                                                <input type="number" class="form-control"
                                                    name="data[{{ $key }}][{{ $key2 }}]"
                                                    value="{{ $inputItem }}"
                                                    style="{{ $key == $key2 ? 'background-color: gray' : '' }}" readonly>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
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
        /**
         * submit form
         */
        const submitForm = () => {
            const form = document.getElementById('weightCriteriaForm')
            form.submit()
        }
    </script>

@endsection
