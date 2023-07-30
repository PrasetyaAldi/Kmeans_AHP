<?php

namespace Services;

use App\Models\Centroid;
use App\Models\KmeansData;
use App\Models\Normalization;
use App\Models\WeightAlternatif;

/**
 * Service for k-means
 */
class KmeansService
{
    protected $columnToNormalize = [
        'jumlah_pekerja',
        'jenis_produksi',
        'kapasitas_produksi',
        'harga_satuan',
        'nilai_produksi',
        'nilai_investasi',
        'umur',
        'pendidikan',
        'surat_izin',
        'motif',
    ];

    /**
     * Menyimpan data
     * 
     * @param array $data
     * 
     */
    public function saveKmeans(array $data)
    {
        $kmeans = new KmeansData();

        $kmeans->fill($data);
        $kmeans->save();

        return $kmeans;
    }

    /**
     * get list data
     * 
     * @param int $paginate
     * 
     * @return KmeansData
     */
    public function getListData(int $paginate = 10, bool $isCount = false)
    {
        if (!$isCount) {
            return KmeansData::orderBy('id')->paginate($paginate);
        }
        return KmeansData::count();
    }

    /**
     * Normalization
     * 
     */
    public function normalizationData()
    {
        set_time_limit(1000);
        $kMeans = new KmeansData();
        $normalize = new Normalization();
        $kMeans = $kMeans->all();

        $columnToNormalize = $this->columnToNormalize;

        $dataNormalize = [];

        // min max normalization
        foreach ($kMeans as $kmean) {
            $data['data_id'] = $kmean->id;
            foreach ($kmean->getAttributes() as $column => $value) {
                if (in_array($column, $columnToNormalize)) {
                    $data[$column] = ($value - $kMeans->min($column)) / ($kMeans->max($column) - $kMeans->min($column));
                }
            }
            $dataNormalize[] = $data;
        }
        // insert into normalized data
        $normalize->insert($dataNormalize);

        return $dataNormalize;
    }

    /**
     * reset cluster
     * 
     */
    public function resetCluster()
    {
        return Centroid::truncate();
    }

    /**
     * get initial centroid
     * 
     * 
     */
    public function getInitialCentroid()
    {
        $normalize = new Normalization();

        $normalize = $normalize->inRandomOrder()->take(3)->get();

        $centroids = [];
        foreach ($normalize as $index => $data) {
            $centroids[] = $data->getAttributes();
        }

        return $centroids;
    }

    /**
     * Calculate distance
     * 
     * @param array $data
     * @return float
     */
    public function calculateDistance($centroid, $data)
    {
        // calculate distance with manhattan
        $distance = 0;
        $columnToNormalize = $this->columnToNormalize;
        foreach ($centroid as $key => $value) {
            if (in_array($key, $columnToNormalize)) {
                $distance += abs($value - $data[$key]);
            }
        }

        return $distance;
    }

    /**
     * update centroids
     * 
     * @param array $clusters
     * @return array
     */
    public function updateCentroids(array $clusters)
    {
        $centroids = [];

        foreach ($clusters as $cluster) {
            $centroid = [];
            foreach ($cluster['data'] as $data) {
                foreach ($data as $key => $value) {
                    if (!isset($centroid[$key])) {
                        $centroid[$key] = 0;
                    }
                    $centroid[$key] += $value;
                }
            }

            foreach ($centroid as $key => $value) {
                $centroid[$key] = $value / count($cluster['data']);
            }
            $centroids[] = $centroid;
        }

        return $centroids;
    }

    /**
     * get list kmeans data normalization
     * 
     */
    public function getNormalization(): array
    {
        $normalize = Normalization::all()->toArray();

        // hanya jika normalize kosong
        if (empty($normalize)) {
            $this->normalizationData();
            $normalize = Normalization::all()->toArray();
        }

        return $normalize;
    }

    /**
     * Mendapatkan list cluster
     * 
     * @param int $paginare
     */
    public function getCluster(int $paginate = 10)
    {
        $cluster = Centroid::selectRaw('cluster, count(*) as jumlah, nilai_sse')->groupBy('cluster', 'nilai_sse')->orderBy('jumlah', 'desc');
        return $cluster->paginate($paginate);
    }

    /**
     * process k-means
     * 
     */
    public function processKmeans(array $data, array $centroids, int $maxIterations = 100)
    {
        $iterations = 0;
        $clusters = [];
        $sumSSE = [];

        while ($iterations < $maxIterations) {
            $clusters = [];

            foreach ($data as $item) {

                $minDistance = PHP_INT_MAX;
                $closestCentroid = null;

                foreach ($centroids as $index => $centroid) {
                    $distance = $this->calculateDistance($centroid, $item);

                    if ($distance < $minDistance) {
                        $minDistance = $distance;
                        $closestCentroid = $index;
                    }
                }
                $sumSSE[$iterations][] = $minDistance;
                $clusters[$closestCentroid]['data'][] = $item;
            }

            $oldCentroids = $centroids;
            $centroids = $this->updateCentroids($clusters);

            // hanya jika centroid tidak berubah
            if ($oldCentroids === $centroids && $iterations = 15) {
                break;
            }
            $iterations++;
        }

        $sse = $this->calculateSSE($sumSSE);

        // menyimpan ke database
        $centroidModel = new Centroid();
        foreach ($clusters as $key => $value) {
            foreach ($value['data'] as $item) {
                $centroidModel->create([
                    'normalize_id' => $item['id'],
                    'cluster' => 'C' . $key + 1,
                    'nilai_sse' => $sse
                ]);
            }
        }

        return ['clusters' => $clusters];
    }

    /**
     * calculate SSE
     * 
     * @param array $data
     * 
     */
    public function calculateSSE(array $data)
    {
        $sumSSE = [];
        foreach ($data as $key => $value) {
            $sumSSE[$key] = array_sum($value);
        }
        $minSSE = min($sumSSE);
        return $minSSE;
    }

    /**
     * Normalize data
     * 
     * @param int $value
     * @param string $column
     * @return float
     */
    private function normalize($value, $column)
    {
        $kMeans = new KmeansData();
        $kMeans = $kMeans->all();

        $max = $kMeans->max($column);
        $min = $kMeans->min($column);

        return ($value - $min) / ($max - $min);
    }

    /**
     * Mendapatkan nama cluster
     * 
     */
    public function getClusterName()
    {
        return Centroid::selectRaw('cluster, COUNT(1) as jumlah ')->groupBy('cluster')->orderBy('cluster')->get();
    }

    /**
     * Check data
     * 
     * 
     */
    public function checkData(): void
    {
        $data = KmeansData::all();
        // hanya jika data tidak kosong
        if ($data->count() > 0) {
            KmeansData::truncate();
            Centroid::truncate();
            Normalization::truncate();
            WeightAlternatif::truncate();
        }
    }
}
