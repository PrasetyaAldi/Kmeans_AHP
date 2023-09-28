@extends('layouts.app')

@section('title', 'Cluster')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <h3 class="card-title">Halaman Cluster</h3>
        <button type="button" data-bs-toggle="modal" data-bs-target="#optimasi_cluster" class="btn btn-success">
            <i class="fa-sharp fa-solid fa-gear"></i> Process Cluster
        </button>
    </div>
    @foreach ($data as $key => $item)
        <div class="card mb-3" style="border: 1px solid #eef2f5">
            <div class="card-header">
                {{-- <div ></div> --}}
                <h3 class="card-title">Cluster {{ $key }}</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            @foreach ($columns as $column)
                                <th>{{ $column }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($item as $row)
                            <tr>
                                @foreach ($columns as $column)
                                    <td>{{ $row[str_replace(' ', '_', strtolower($column))] }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
@endsection

@section('modal')
    <!-- Modal -->
    <div class="modal fade" id="optimasi_cluster" tabindex="-1" aria-labelledby="optimasi_cluster" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('process') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="optimasi_cluster">Tentukan Cluster</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="banyak_cluster" class="form-label">Banyak Cluster</label>
                            <input type="number" required class="form-control" id="banyak_cluster" name="banyak_cluster"
                                placeholder="Masukkan banyak cluster yang diinginkan">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
