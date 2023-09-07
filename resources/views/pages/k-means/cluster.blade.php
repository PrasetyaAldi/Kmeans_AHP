@extends('layouts.app')

@section('title', 'Cluster')

@section('content')
    @php
        $label = $data->pluck('name_cluster')->toArray();
        $sse = $data->pluck('nilai_sse')->toArray();
    @endphp
    <div class="card" style="border: 1px solid #eef2f5">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <h3 class="card-title">Cluster</h3>
                <button type="button" data-bs-toggle="modal" data-bs-target="#optimasi_cluster" class="btn btn-success">
                    <i class="fa-sharp fa-solid fa-gear"></i> Process Cluster
                </button>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nama Cluster</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $item)
                        <tr>
                            <td>{{ $item->cluster }}</td>
                            <td>{{ $item->jumlah }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center">Data Tidak ditemukan</td>
                        </tr>
                    @endforelse
                    @if (!empty($data->items()))
                        <tr>
                            <th>Total</th>
                            <th>{{ array_reduce(
                                $data->items(),
                                function ($carry, $item) {
                                    return $carry + $item->jumlah;
                                },
                                0,
                            ) }}
                            </th>
                        </tr>
                        <tr>
                            <th>Nilai SSE</th>
                            <th>{{ $data->first()->nilai_sse }}</th>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        @if ($data->hasPages())
            <div class="card-footer bg-white">
                {{ $data->links() }}
            </div>
        @endif
    </div>
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
