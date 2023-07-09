@extends('layouts.app')

@section('title', 'Cluster')

@section('content')
    <div class="card" style="border: 1px solid #eef2f5">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between">
                <h3 class="card-title">Cluster</h3>
                @if (!empty($data->items()))
                    <form action="{{ route('process') }}" method="POST">
                        @csrf
                        <input type="hidden" name="reset_cluster" value="{{ true }}">
                        <button type="submit" class="btn btn-success">
                            <i class="fa-sharp fa-solid fa-gear"></i> Perbarui Cluster
                        </button>
                    </form>
                @else
                    <form action="{{ route('process') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="fa-sharp fa-solid fa-gear"></i> Process Cluster
                        </button>
                    </form>
                @endif
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
                            <td>{{ array_reduce(
                                $data->items(),
                                function ($carry, $item) {
                                    return $carry + $item->jumlah;
                                },
                                0,
                            ) }}
                            </td>
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
