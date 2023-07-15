<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Services\AHPService;
use Services\KmeansService;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, AHPService $ahpService)
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
        $data['count_data'] = $kMeansService->getListData(isCount: true);
        $data['count_criteria'] = $ahpService->getCriteria()->count();
        $data['active'] = $cluster;
        return view('pages.home.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
