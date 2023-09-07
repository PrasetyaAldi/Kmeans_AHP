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
                <h3 class="card-title">Mencari Cluster Terbaik</h3>
                <button type="button" data-bs-toggle="modal" data-bs-target="#optimasi_cluster" class="btn btn-success">
                    <i class="fa-sharp fa-solid fa-gear"></i> Optimasi Cluster
                </button>
            </div>
        </div>
        <div class="card-body">
            @if (!empty($data))
                <table class="table table-stripped">
                    <thead>
                        <tr>
                            <th>Banyak Cluster</th>
                            <th>Data Cluster</th>
                            <th>Nilai SSE</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>
                                <td>{{ $item->name_cluster }}</td>
                                <td>
                                    <div class="row">
                                        @foreach ($item->data_cluster as $key => $value)
                                            <div class="col-4">
                                                {!! '<strong>' . $key . '</strong>' . ' = ' . $value !!}
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td>{{ $item->nilai_sse }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
    <div class="card mt-5">
        <div class="card-header">
            <h3 class="card-title">Chart SSE</h3>
        </div>
        <div class="card-body">
            <canvas id="myChart" class="chart" data-type='line' data-labels="[red, green, blue, white, yellow, black]"
                data-series="[12, 19, 3, 5, 2, 3]" data-label="SSE" width="400" height="150"></canvas>
        </div>
    </div>
@endsection

@section('modal')
    <!-- Modal -->
    <div class="modal fade" id="optimasi_cluster" tabindex="-1" aria-labelledby="optimasi_cluster" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('process-optimasi') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="optimasi_cluster">Optimasi Cluster</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="banyak_cluster" class="form-label">Banyak Cluster</label>
                            <input type="number" class="form-control" id="banyak_cluster" name="banyak_cluster"
                                placeholder="Masukkan banyak cluster yang diinginkan">
                            <div id="cluster_help" class="form-text">Jika tidak diisi maka akan memproses cluster sebanyak
                                akar(n/2)*</div>
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

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.querySelectorAll(".chart").forEach(function(element) {
            let type = element.getAttribute("data-type");
            let labels = element.getAttribute("data-labels").split(",");
            let series = element.getAttribute("data-series").split(",");
            let bgColor = element.getAttribute("data-bg-color");
            let borderColor = element.getAttribute("data-border-color");
            let color = element.getAttribute("data-color");
            let options = JSON.parse(element.getAttribute("data-options"));
            let label = element.getAttribute("data-label")
            let ctx = element.getContext("2d");

            new Chart(ctx, {
                type: type,
                data: {
                    labels: {!! json_encode($label) !!},
                    datasets: [{
                        label: label,
                        data: {!! json_encode($sse) !!},
                        backgroundColor: bgColor,
                        borderColor: borderColor,
                        color: color,
                    }, ],
                },
                options: options,
            });
        });
    </script>
@endsection
