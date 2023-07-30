<?php

namespace App\Http\Controllers;

use App\Imports\KmeansImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Services\KmeansService;

class KmeansController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(KmeansService $kmeansService)
    {
        $kmeansData = $kmeansService->getListData();
        $data['data'] = $kmeansData;
        $data['columns'] = [
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

        return view('pages.k-means.index', $data);
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

        // normalization data
        $kmeansService->normalizationData();

        return redirect(route('k-means.index'))->with('success', 'Import Data Berhasil');
    }

    /**
     * Get centroid
     * 
     */
    public function processCluster(Request $request, KmeansService $kmeansService)
    {
        $message = 'Process Cluster Berhasil';
        if ($request->has('reset_cluster') && $request->reset_cluster) {
            $kmeansService->resetCluster();
            $message = 'Update Cluster Berhasil';
        }
        $centroids = $kmeansService->getInitialCentroid();

        $normalizations = $kmeansService->getNormalization();

        // // process
        $data = $kmeansService->processKmeans($normalizations, $centroids);
        return redirect()->to(route('cluster'))->with('success', $message);
    }
}
