<?php

namespace App\Http\Controllers;

use App\Imports\KmeansImport;
use App\Models\KmeansDataReal;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Services\KmeansService;

class KmeansController extends Controller
{
    protected $columns = [
        'Nama Pemilik',
        'Jumlah Pekerja',
        'Jenis Produksi',
        'Kapasitas Produksi',
        'Harga Satuan',
        'Nilai Produksi',
        'Nilai Investasi',
        'Umur',
        'Pendidikan',
        'Surat Izin',
        'Motif',
    ];
    /**
     * Display a listing of the resource.
     */
    public function index(KmeansService $kmeansService)
    {
        $kmeansDataReal = $kmeansService->getListDataReals();
        $data['data'] = $kmeansDataReal;
        $data['columns'] = $this->columns;

        return view('pages.k-means.index', $data);
    }

    public function indexNormalization(KmeansService $kmeansService)
    {
        $kmeansDataReal = $kmeansService->getListNormalization();
        if (empty($kmeansDataReal->items())) {
            $kmeansDataReal = $kmeansService->getListData();
        }
        $data['data'] = $kmeansDataReal;
        $data['columns'] = $this->columns;
        $data['buttonAction'] = [
            ['label' => 'Proses Normalisasi', 'route' => route('k-means.proses-normalization')]
        ];

        return view('pages.k-means.data', $data);
    }

    public function indexTransformation(KmeansService $kmeansService)
    {
        $kmeansDataReal = $kmeansService->getListData();
        if (empty($kmeansDataReal->items())) {
            $kmeansDataReal = $kmeansService->getListDataReals();
        }
        $data['data'] = $kmeansDataReal;
        $data['columns'] = $this->columns;
        $data['buttonAction'] = [
            ['label' => 'Proses Transformasi', 'route' => route('k-means.proses-transformation')]
        ];

        return view('pages.k-means.data', $data);
    }

    public function processTransformation(KmeansService $kmeansService)
    {
        // hanya jika table transformation tidak kosong
        if (!empty($kmeansService->getListData()->items())) {
            return redirect()->back()->with('error', 'Data sudah di transformasi');
        }
        try {
            $kmeansService->procesLabelEncoding();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->back()->with('success', 'Berhasil Melakukan proses transformasi');
    }

    public function processNormalization(KmeansService $kmeansService)
    {
        // hanya jika table normalization tidak kosong
        if (!empty($kmeansService->getListNormalization()->items())) {
            return redirect()->back()->with('error', 'Data sudah normalisasi');
        }

        try {
            $kmeansService->normalizationData();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->back()->with('success', 'Berhasil Melakukan Normal');
    }

    public function optimasiCluster(KmeansService $kmeansService)
    {
        $data['data'] = $kmeansService->getTempCluster();
        return view('pages.k-means.optimasi_cluster', $data);
    }

    public function cluster(KmeansService $kmeansService)
    {
        $data['data'] = $kmeansService->getCluster();
        return view('pages.k-means.cluster', $data);
    }

    /**
     * Import data from excel file.
     * 
     * @param Request $request
     * @param KmeansService $kmeansService
     */
    public function import(Request $request, KmeansService $kmeansService)
    {
        $data = $request->file('file');
        // hanya jika data kosong
        if (is_null($data)) {
            return redirect(route('k-means'))->with('error', 'Data Excel is required');
        }

        $excel = Excel::toArray(new KmeansImport, $data);

        $kmeansService->checkData();

        // looping sebanyak data excel
        foreach ($excel[0] as $key => $value) {
            $kmeansService->saveKmeans($value);
        }

        return redirect(route('k-means.index'))->with('success', 'Import Data Berhasil');
    }

    /**
     * Get centroid
     * 
     */
    public function processOptimasiCluster(Request $request, KmeansService $kmeansService)
    {
        $kmeansService->resetCluster();
        $centroids = $kmeansService->getInitialCentroid($request->banyak_cluster);

        $normalizations = $kmeansService->getNormalization();

        // process
        $kmeansService->searchBestCluster($normalizations, $centroids);

        // $data = $kmeansService->processKmeans($normalizations, $centroids);
        return redirect()->to(route('optimasi-cluster'))->with('success', 'Berhasil menghitung optimasi cluster');
    }

    /**
     * Get centroid
     * 
     */
    public function processCluster(Request $request, KmeansService $kmeansService)
    {
        $message = 'Process Cluster Berhasil';
        $kmeansService->resetCluster(false);
        $centroids = $kmeansService->getInitialCentroid($request->banyak_cluster, false);

        $normalizations = $kmeansService->getNormalization();

        // process
        $kmeansService->processKmeans($normalizations, $centroids);

        return redirect()->to(route('cluster'))->with('success', $message);
    }
}
