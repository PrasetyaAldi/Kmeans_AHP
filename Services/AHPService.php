<?php

namespace Services;

use App\Models\Centroid;
use App\Models\WeightAlternatif;
use App\Models\WeightCriteria;
use Exception;

class AHPService
{
    /**
     * menghitung nilai bobot kriteria
     * 
     * @param array $criteria
     * 
     */
    public function getCriteriaWeight($criteria): array
    {
        // random index
        $randomIndex = [
            1 => 0,
            2 => 0,
            3 => 0.58,
            4 => 0.9,
            5 => 1.12,
            6 => 1.24,
            7 => 1.32,
            8 => 1.41,
            9 => 1.45,
            10 => 1.49
        ];

        // menghitung jumlah total setiap kolom
        $sum = [];
        foreach ($criteria as $key => $value) {
            $total = 0;
            foreach ($criteria as $item) {
                $total += $item['penilaian'][$key];
            }
            $sum[] = $total;
        }

        // Normalisasi matrix perbandingan berpasangan
        $normalization = [];
        foreach ($criteria as $key => $value) {
            $row = [];
            foreach ($value['penilaian'] as $k => $v) {
                $tests[] = ['v' => $v, 'sum' => $sum[$k], 'total' => $v / $sum[$k]];
                $row[] = $v / $sum[$k];
            }
            $normalization[] = $row;
        }

        // menghitung rata-rata dari setiap baris
        $rowAverages = [];
        foreach ($normalization as $evaluationNormalized) {
            $rowAverages[] = array_sum($evaluationNormalized) / count($evaluationNormalized);
        }

        // Menghitung bobot relatif
        $totalRowAverage = array_sum($rowAverages);
        $weights = [];
        foreach ($rowAverages as $average) {
            $weight = $average / $totalRowAverage;
            $weights[] = $weight;
        }

        // menghitung eigen value
        $eigenValue = [];
        foreach ($weights as $key => $weight) {
            $eigenValue[] = $weight * $sum[$key];
        }

        // menyimpan ke table bobot_criteria
        $this->saveCriteriaWeight($criteria, $weights, $eigenValue);

        $eigenValue = array_sum($eigenValue);
        $ri = $randomIndex[count($criteria)];

        // menghitung nilai CI
        $ci = ($eigenValue - count($criteria)) / (count($criteria) - 1);

        // menghitung nilai CR
        $cr = $ci / $ri;

        // hanya jika nilai CR > 1
        if ($cr > 1) {
            throw new Exception('Nilai CR > 1');
        }

        return ['consistency_index' => $ci, 'consistency_ratio' => $cr, 'weights' => $weights];
    }

    /**
     * Menyimpan hasil perhitungan bobot kriteria
     * 
     * @param array $criteria
     * @param array $weights
     * @param array $eigenValue
     * 
     */
    public function saveCriteriaWeight($criteria, $weights, $eigenValue): void
    {
        $weightCriteria = new WeightCriteria;

        foreach ($criteria as $key => $value) {
            $weightCriteria->create([
                'criteria_id' => $value['id'],
                'eigen_value' => $eigenValue[$key],
                'bobot' => $weights[$key],
            ]);
        }
    }

    /**
     * menghitung nilai bobot alternatif
     * 
     * @param array $alternatif
     * @param int|null $criteriaId
     * 
     */
    public function getAlternatifWeight(array $alternatif, int $criteriaId = 1, string $cluster = 'C3'): array
    {
        // count all alternatif in column
        $sum = [];
        foreach ($alternatif as $key => $value) {
            $total = 0;
            foreach ($alternatif as $item) {
                $total += $item[$key];
            }
            $sum[] = $total;
        }

        // normalization
        $normalization = [];
        foreach ($alternatif as $key => $value) {
            $row = [];
            foreach ($value as $k => $v) {
                $tests[] = ['v' => $v, 'sum' => $sum[$k], 'total' => $v / $sum[$k]];
                $row[] = $v / $sum[$k];
            }
            $normalization[] = $row;
        }

        // average
        $rowAverages = [];
        foreach ($normalization as $evaluationNormalized) {
            $rowAverages[] = array_sum($evaluationNormalized) / count($evaluationNormalized);
        }

        // simpan ke database
        $this->saveAlternatifWeight($alternatif, $rowAverages, $cluster, $criteriaId);
        return ['eigen_value' => $rowAverages];
    }

    /**
     * menyimpan ke table nilai_alternatif
     */
    public function saveAlternatifWeight(array $alternatif,  array $eigenValue, string $cluster = 'C3', int $criteriaId = 1): void
    {
        // get alternatif data from cluster
        $alternatives = Centroid::where('cluster', $cluster)->pluck('normalize_id')->toArray();
        // pairing alternatif with alternatives

        $pairs = [];
        foreach ($alternatives as $key => $value) {
            foreach ($alternatives as $k => $v) {
                $pairs[$value][$v] = $alternatif[$key][$k];
            }
            $pairs[$value]['eigen_value'] = $eigenValue[$key];
        }

        $alternatives = $pairs;

        // save to table
        $weightAlternatif = new WeightAlternatif;
        foreach ($alternatives as $key => $value) {
            $weightAlternatif->create([
                'normalize_id' => $key,
                'criteria_id' => $criteriaId,
                'eigen_value' => $value['eigen_value'],
            ]);
        }
    }
}
