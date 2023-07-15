@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
    <div class="row text-center">
        @if (!empty($count_data))
            <div class="col-md-4 col-sm-12">
                <div class="card"
                    style="background-image: linear-gradient(to right bottom, var(--bs-primary), var(--bs-primary-bg-subtle));">
                    <div class="card-body">
                        <div>
                            <h3 class="fw-bold text-light">Data</h3>
                            <h4 class="fw-bold">{{ $count_data }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if (!empty($clusters))
            <div class="col-md-4 col-sm-12">
                <div class="card"
                    style="background-image: linear-gradient(to right bottom, var(--bs-success), var(--bs-success-bg-subtle));">
                    <div class="card-body">
                        <div>
                            <h3 class="fw-bold text-light">Cluster</span>
                                <h4 class="fw-bold">{{ count($clusters) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if (!empty($count_criteria))
            <div class="col-md-4 col-sm-12">
                <div class="card"
                    style="background-image: linear-gradient(to right bottom, var(--bs-info), var(--bs-info-bg-subtle));">
                    <div class="card-body">
                        <div>
                            <h3 class="fw-bold text-light">Kriteria</h3>
                            <h4 class="fw-bold">{{ $count_criteria }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <div class=" mt-4">
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h3>Cluster</h3>
                        <hr>
                        <x-sidebar :data="$clusters" :active="$active" :column_key="'cluster'" :column_val="'cluster'" />
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Peringkat per Cluster</h3>
                        <hr>
                        @if (empty($rank))
                            <div class="alert alert-warning">
                                <h4 class="text-center">Data Bobot Alternatif Belum di Hitung</h4>
                            </div>
                        @else
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Peringkat</th>
                                        <th>Nama Pemilik</th>
                                        <th>Nilai AHP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($rank as $item)
                                        <tr>
                                            <td>{{ $item['ranked'] }}</td>
                                            <td>{{ $item['nama'] }}</td>
                                            <td>{{ $item['rank'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                    {{-- hanya jika ada pagination --}}
                    @if (!empty($rank) && $rank->hasPages())
                        <div class="card-footer">
                            {{ $rank->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        /**
         * pilih criteria
         */
        const selectCriteria = (clusterId) => {
            const url = new URL(window.location.href)
            const urlSearch = new URLSearchParams(url.search)
            if (urlSearch.has('cluster')) {
                urlSearch.delete('cluster')
            }
            urlSearch.set('cluster', clusterId)
            url.search = urlSearch.toString()

            window.location.href = url
        }
    </script>
@endsection
