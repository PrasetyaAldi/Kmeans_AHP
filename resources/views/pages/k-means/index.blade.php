@extends('layouts.app')

@section('title', 'K-Means')

@section('header')
    <div class="row">
        <div class="col-12">
            <h4 class="text-start">Data</h4>
        </div>
    </div>
@endsection

@section('content')
    <div class="card shadow" style="border:0px">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <h3 class="card-title">Data Batik</h3>
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    <i class="fa-solid fa-plus"></i> Upload Excel
                </button>
            </div>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        @foreach ($columns as $column)
                            <th>{{ $column }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $item)
                        <tr>
                            @foreach ($columns as $column)
                                <td>{{ $item->{strtolower(str_replace(' ', '_', $column))} }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center">Data Tidak ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $data->links() }}
        </div>
    </div>
@endsection

@section('modal')
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id='form' action="{{ route('k-means.import') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="formFile" class="form-label">Upload Excel</label>
                            <input class="form-control" name="file" type="file" id="formFile"
                                accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="submitForm()">Submit</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const submitForm = () => {
            document.getElementById('form').submit();
        }
    </script>
@endsection
