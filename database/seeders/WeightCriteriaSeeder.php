<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WeightCriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->makeComparison();
    }

    /**
     * make comparison for weight criteria.
     */
    public function makeComparison(): void
    {
        $ahpService = new \Services\AHPService();
        $criteria = \App\Models\Criteria::all();
        $criteria = $criteria->pluck('name')->toArray();

        // make comparison for weight criteria
        $input = [];
        foreach ($criteria as $key => $value) {
            foreach ($criteria as $key1 => $value1) {
                if ($value == $value1) {
                    $input[$key][$key1]['value'] = 1;
                    $input[$key][$key1]['col'] = $key1;
                    $input[$key][$key1]['row'] = $key;
                } else {
                    $input[$key][$key1]['value'] = rand(1, 9);
                    $input[$key][$key1]['col'] = $key1;
                    $input[$key][$key1]['row'] = $key;
                }
            }
        }

        $data = [];
        foreach ($input as $key => $value) {
            $temp = [];
            foreach ($value as $value1) {
                if ($value1['col'] != $value1['row']) {
                    $input[$value1['col']][$value1['row']]['value'] = 1 / $input[$value1['row']][$value1['col']]['value'];
                }
                $temp[] = $input[$value1['col']][$value1['row']]['value'];
            }
            $data[] = $temp;
        }

        $temp = [];
        foreach ($data as $key => $value) {
            $temp[] = [
                'id' => $key + 1,
                'penilaian' => $value
            ];
        }
        $ahpService->resetWeightCriteria();
        $ahpService->getCriteriaWeight($temp);
    }
}
