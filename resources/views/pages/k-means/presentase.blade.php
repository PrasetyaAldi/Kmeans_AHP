@extends('layouts.app')

@section('title', 'Cluster')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Presentase Cluster</h3>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Cluster</th>
                        @foreach ($columns as $column)
                            <th>{{ $column }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $key => $item)
                        <tr>
                            <td>Cluster {{ $key }}</td>
                            @foreach ($columns as $column)
                                <td>{{ number_format($item[str_replace(' ', '_', strtolower($column))], 2, ',', '') . '%' }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
