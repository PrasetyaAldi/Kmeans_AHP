<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Services\AHPService;

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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
