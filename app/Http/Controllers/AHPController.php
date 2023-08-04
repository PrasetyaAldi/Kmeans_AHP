<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Services\AHPService;
use Services\KmeansService;

class AHPController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(AHPService $ahpService)
    {
        $data['data'] = $ahpService->pairwiseComparisonCriteria();
        $data['weight_criteria'] = $ahpService->getAllWeightCriteria();
        return view('pages.ahp.index', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, AHPService $ahpService)
    {
        $criteria = $request->data;
        $data = [];
        foreach ($criteria as $key => $value) {
            $data[] = [
                'id' => $key + 1,
                'penilaian' => $value
            ];
        }
        try {
            $ahpService->getCriteriaWeight($data);
        } catch (\Exception $e) {
            return redirect()->back()->withInput($request->input())->with('error', $e->getMessage());
        }
        return redirect()->route('ahps.index')->with('success', 'Berhasil menghitung bobot kriteria');
    }

    /**
     * reset AHP
     * 
     * @param AHPService $ahpService
     * 
     */
    public function resetWeightCriteria(AHPService $ahpService)
    {
        try {
            $ahpService->resetWeightCriteria();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->back()->with('success', 'Berhasil me-reset bobot kriteria. Silahkan inputkan bobot kembali.');
    }

    /**
     * weight alternatif
     * 
     */
    public function weightAlternatif(Request $request, AHPService $ahpService)
    {
        $criteria = $request->criteria ?? '1';
        $cluster = $request->cluster ?? 'C1';
        $kMeansService = new KmeansService;
        $alternatifWeight = $ahpService->getAllWeightAlternatif($criteria, $cluster);
        $data['data'] = $ahpService->getAlternatif($cluster);
        $data['criterias'] = $ahpService->getCriteria();
        $data['clusters'] = $kMeansService->getClusterName();
        $data['select_cluster'] = $cluster;
        $data['active'] = (int)$criteria;
        $data['alternatif_weight'] = $alternatifWeight;
        return view('pages.ahp.alternatif', $data);
    }

    /**
     * Store weight alternatif
     * 
     * @param Request $request
     * @param AHPService $ahpService
     * 
     */
    public function storeWeightAlternatif(Request $request, AHPService $ahpService)
    {
        $data = json_decode($request->json_data, true);
        $criteria_id = $request->criteria_id;
        $cluster = $request->cluster;

        try {
            $ahpService->getAlternatifWeight($data, $criteria_id, $cluster);
        } catch (\Exception $e) {
            return redirect()->back()->withInput($request->input())->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Berhasil menghitung bobot alternatif');
    }

    /**
     * reset weight alternatif
     * 
     * @param Request $request
     * @param AHPService $ahpService
     * 
     */
    public function resetWeightAlternatif(Request $request, AHPService $ahpService)
    {
        $cluster = $request->cluster;
        $criteria_id = $request->criteria_id;

        try {
            $ahpService->resetWeightAlternatif($criteria_id, $cluster);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->back()->with('success', 'Berhasil me-reset bobot alternatif. Silahkan inputkan bobot kembali.');
    }

    /**
     * finalisasi perhitungan
     * 
     * @param Request $request
     * @param AHPService $ahpService
     * 
     */
    public function finalCalculate(Request $request, AHPService $ahpService)
    {
        $cluster = $request->cluster ?? 'C1';
        $finalResult = $ahpService->finalResult($cluster);
        // hanya jika final result tidak empty
        if (!empty($finalResult)) {
            // memberikan paginasi
            $finalResult = collect($finalResult);

            $page = $request->page ?? 1;
            $perPage = 10;
            $offset = ($page * $perPage) - $perPage;
            $itemsForCurrent = $finalResult->slice($offset, $perPage)->all();
            $finalResult = new \Illuminate\Pagination\LengthAwarePaginator(
                $itemsForCurrent,
                $finalResult->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        }
        $data['rank'] = $finalResult;
        $kMeansService = new KmeansService;
        $data['clusters'] = $kMeansService->getClusterName();
        $data['active'] = $cluster;
        return view('pages.ahp.final', $data);
    }

    /**
     * final Result
     * 
     * @param Request $request
     * @param AHPService $ahpService
     * 
     */
    public function finalResult($cluster, AHPService $ahpService)
    {
        $cluster = $cluster ?? 'C1';
        try {
            $ahpService->finalResult($cluster);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Berhasil menghitung hasil akhir');
    }

    /**
     * get data alternatif
     * 
     */
    public function getDataAlternatif(Request $request, AHPService $ahpService)
    {
        $cluster = $request->cluster ?? 'C1';
        $data = $ahpService->getAlternatif($cluster);
        return response()->json($data);
    }
}
