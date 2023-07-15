@extends('layouts.app')

@section('title', 'Peringkat')

@section('content')
    <div class="row">
        <div class="col-3">
            <div class="card">
                <div class="card-body">
                    <h4 class="text-center">Cluster</h4>
                    <hr>
                    <x-sidebar :data="$clusters" :active="$active" :column_key="'cluster'" :column_val="'cluster'" />
                </div>
            </div>
        </div>
        <div class="col-12 col-md-9 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h3>Ranking</h3>
                    </div>
                </div>
                <div class="card-body">
                    {{-- hanya jika rank null --}}
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
