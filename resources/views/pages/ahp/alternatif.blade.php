@extends('layouts.app')

@section('title', 'AHP')

@section('content')
    <div class="row">
        <div class="col-3">
            <div class="card d-none d-md-block">
                <div class="card-body">
                    <h3 class="text-center">Kategori</h3>
                    <hr>
                    <x-sidebar :data="$criterias" :active="$active" />
                </div>
            </div>
        </div>
        <div class="col-12 col-md-9 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h3>Menghitung Bobot Alternatif</h3>
                        <div class="d-flex align-items-center">
                            <span class="me-2 fw-semibold">Pilih Cluster :</span>
                            <form class="formCluster">
                                <select name="cluster" class="form-control">
                                    @foreach ($clusters as $cluster)
                                        <option value="{{ $cluster['cluster'] }}"
                                            {{ $cluster['cluster'] == $select_cluster ? 'selected' : '' }}>
                                            {{ $cluster['cluster'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if (!empty($alternatif_weight->items()))
                        <div class="alert alert-primary" role="alert">
                            Nilai CR nya : {{ $alternatif_weight->first()->cr }}
                        </div>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nama Alternatif</th>
                                    <th>Eigen Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($alternatif_weight as $item)
                                    <tr>
                                        <td>{{ $item->data->nama_pemilik }}</td>
                                        <td>{{ $item->eigen_value }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div style="width:100%;overflow-x:scroll">
                            <form action="{{ route('ahps.store-weight-alternatif') }}" method="post"
                                class="formWeightAlternatif">
                                @csrf
                                <input type="hidden" name="criteria_id" value="{{ $active }}">
                                <input type="hidden" name="cluster" value="{{ $select_cluster }}">
                                <input type="hidden" name="json_data" value="" id="json_data">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            @foreach ($data as $item)
                                                <th>{{ $item->data->nama_pemilik }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody id="dynamic-tbody">
                                        <!-- Tampilkan spinner saat proses render data belum selesai -->
                                        <tr>
                                            <td colspan="{{ count($data) + 1 }}">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                                <span>Menunggu Render Data Selesai...</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    @if (!empty($alternatif_weight->items()))
                        <div class="d-flex justify-content-between">
                            <form action="{{ route('ahps.reset-weight-alternatif') }}" method="post">
                                @csrf
                                <input type="hidden" name="criteria_id" value="{{ $active }}">
                                <input type="hidden" name="cluster" value="{{ $select_cluster }}">
                                <button type="submit" class="btn btn-warning"
                                    style="background-color: var(--bs-warning-bg-subtle)"><i class="fa-solid fa-gear"></i>
                                    Hitung
                                    Ulang</button>
                            </form>
                            {{ $alternatif_weight->links() }}
                        </div>
                    @else
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-primary" onclick="submitFormWieghtAlternatif()">
                                <i class="fa-solid fa-floppy-disk"></i> Submit
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // hanya jika alternatif weight empty
            @if (empty($alternatif_weight->items()))
                fetch(`{{ route('ahps.data-alternatif') }}?cluster={{ $select_cluster }}`).then(res => res.json())
                    .then(res => {
                        const clusterId = document.querySelector('select[name="cluster"]').value
                        const tbody = document.getElementById('dynamic-tbody')
                        const data = res;
                        tbody.innerHTML = ''

                        data.forEach((item, key) => {
                            const newRow = document.createElement('tr');
                            newRow.innerHTML = `<th class="headcol">${item.data.nama_pemilik}</th>`
                            data.forEach((item2, key2) => {
                                const isReadOnly = key == key2 ? 'readonly' : ''
                                const bgColor = key == key2 ? 'background-color: gray' : ''
                                const value = key == key2 ? 1 : Math.floor(Math.random() * 9) +
                                    1

                                newRow.innerHTML += `<td>
                                    <input type="number" class="form-control" 
                                    name="data[${key}][${key2}]" id="data[${key}][${key2}]"
                                    data-col="${key2}" data-row="${key}" value="${value}" 
                                    min="1" max="9" ${isReadOnly} style="${bgColor}">
                                </td>`
                            })
                            tbody.appendChild(newRow)
                        })
                        const input = document.querySelectorAll('input[type="number"]')
                        input.forEach((item) => {
                            item.addEventListener('change', function() {
                                const id = this.id.split(/[\[\]]/).filter(Boolean)
                                const value = this.value
                                if (value > 9 || value < 1) {
                                    alert('Nilai Maksimal 9 dan Minimal 0')
                                    this.value = 1
                                    value = 1
                                }
                                const input2 = document.getElementById(
                                    `data[${id[2]}][${id[1]}]`)
                                input2.value = 1 / value
                            })
                            const col = item.getAttribute('data-col')
                            const row = item.getAttribute('data-row')
                            if (col != row) {
                                // membuat nilai inputan berpasangan berubah dengan nilai awal di input
                                const input2 = document.getElementById(
                                    `data[${col}][${row}]`)
                                input2.value = 1 / item.value
                            }
                        })
                    }).catch(err => console.log(err))
            @endif
        })

        const cluster = new URLSearchParams(window.location.search).get('cluster');

        // Tambahkan parameter 'cluster' pada URL saat tombol pagination di-klik
        document.querySelectorAll('.pagination a').forEach(link => {
            const href = new URL(link.href);
            href.searchParams.set('cluster', cluster);
            link.href = href.toString();
        });

        /**
         * Submit Form Cluster
         */
        const submitFormCluster = () => {
            const form = document.querySelector('.formCluster')
            form.submit()
        }

        /**
         * Submit Form Weight Alternatif
         */
        const submitFormWieghtAlternatif = () => {
            const jsonData = {}
            const form = document.querySelector('.formWeightAlternatif')
            const inputNumber = document.querySelectorAll('input[type="number"]')
            inputNumber.forEach((inputElement) => {
                const row = inputElement.dataset.row;
                const col = inputElement.dataset.col;
                const value = inputElement.value;

                // Membentuk data ke dalam objek JSON
                if (!jsonData[row]) {
                    jsonData[row] = {};
                }
                jsonData[row][col] = parseFloat(value);
            });
            const hiddenJsonData = document.getElementById('json_data')
            hiddenJsonData.value = JSON.stringify(jsonData)
            form.submit()
        }

        /**
         * pilih criteria
         */
        const selectCriteria = (clusterId) => {
            const url = new URL(window.location.href)
            const urlSearch = new URLSearchParams(url.search)
            if (urlSearch.has('criteria')) {
                urlSearch.delete('criteria')
            }
            urlSearch.set('criteria', clusterId)
            url.search = urlSearch.toString()

            window.location.href = url
        }

        /**
         * pilih cluster
         */
        const mySelect = document.querySelector('select[name="cluster"]')
        mySelect.addEventListener('change', function(event) {
            const url = new URL(window.location.href)
            const urlSearch = new URLSearchParams(url.search)
            if (urlSearch.has('cluster')) {
                urlSearch.delete('cluster')
            }
            urlSearch.set('cluster', this.value)
            url.search = urlSearch.toString()

            window.location.href = url
        })
    </script>
@endsection
