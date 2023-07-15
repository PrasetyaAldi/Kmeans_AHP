<?php

namespace Services;

use App\Models\Centroid;
use App\Models\Criteria;
use App\Models\WeightAlternatif;
use App\Models\WeightCriteria;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AHPService
{
    /**
     * menghitung nilai bobot kriteria
     * 
     * @param array $criteria
     * 
     */
    public function getCriteriaWeight(array $criteria): array
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

        $ri = $randomIndex[count($criteria)];

        // menghitung nilai CI
        $ci = (array_sum($eigenValue) - count($criteria)) / (count($criteria) - 1);

        // menghitung nilai CR
        $cr = $ci / $ri;

        // hanya jika nilai CR > 1
        if ($cr > 1) {
            throw new Exception('Nilai CR > 1');
        }

        // menyimpan ke table bobot_criteria
        $this->saveCriteriaWeight($criteria, $weights, $eigenValue);

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
     * Mendapatkan criteria
     * 
     * @return Collection
     */
    public function getCriteria(): Collection
    {
        return Criteria::all();
    }

    /**
     * mendapatkan data alternatif berdasarkan cluster
     * 
     * @param string $cluster
     * 
     * @return Collection
     */
    public function getAlternatif(string $cluster = 'C1'): Collection
    {
        return Centroid::with('data')->where('cluster', $cluster)->get();
    }

    /**
     * Reset weight criteria
     * 
     * 
     */
    public function resetWeightCriteria()
    {
        return WeightCriteria::truncate();
    }

    /**
     * reset weight alternatif
     * 
     * @param int $criteriaId
     * @param string $cluster
     * 
     * @return void
     */
    public function resetWeightAlternatif(int $criteriaId, string $cluster)
    {
        $centroids = Centroid::where('cluster', $cluster)->pluck('normalize_id')->toArray();

        return WeightAlternatif::whereIn('normalize_id', $centroids)->where('criteria_id', $criteriaId)->delete();
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

    /**
     * get All weight criteria
     */
    public function getAllWeightCriteria()
    {
        return WeightCriteria::with('criteria')->get();
    }

    /**
     * get All weight alternatif
     */
    public function getAllWeightAlternatif(int $criteriaId, string $cluster)
    {
        $centroids = Centroid::where('cluster', $cluster)->pluck('normalize_id')->toArray();

        return WeightAlternatif::with(['data', 'criteria'])->where('criteria_id', $criteriaId)->whereIn('normalize_id', $centroids)->get();
    }

    /**
     * menghitung nilai semua alternatif dan kriteria
     * 
     * @param string $cluster
     * 
     * @return object
     */
    public function finalResult(string $cluster)
    {
        $criteria = $this->getAllWeightCriteria();
        $centroids = Centroid::where('cluster', $cluster)->pluck('normalize_id')->toArray();
        $alternatif = WeightAlternatif::whereIn('normalize_id', $centroids)->get();

        // hanya jika alternatif null
        if ($alternatif->isEmpty()) {
            return [];
        }

        // menghitung nilai akhir
        $result = [];
        foreach ($centroids as $centroid) {
            $row = [];
            foreach ($criteria as $c) {
                $row[] = $alternatif->where('normalize_id', $centroid)->where('criteria_id', $c->criteria_id)->first()->eigen_value * $c->bobot;
            }
            $result[] = $row;
        }

        $finalResult = [];
        foreach ($result as $key => $value) {
            $finalResult[] = array_sum($value);
        }

        // sorting
        usort($finalResult, function ($a, $b) {
            return $a <=> $b;
        });

        // menghitung peringkat alternatif
        $rank = [];
        foreach ($finalResult as $key => $value) {
            // tambahkan peringkat
            $rank[] = ['id' => $centroids[$key], 'rank' => $value, 'nama' => Centroid::find($centroids[$key])->data->nama_pemilik, 'ranked' => $key + 1];
        }

        // return
        return $rank;
    }

    /**
     * pairwise comparison criteria
     * 
     */
    public function pairwiseComparisonCriteria()
    {
        return Criteria::all();
    }
}
