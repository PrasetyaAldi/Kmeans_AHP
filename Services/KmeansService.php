<?php

namespace Services;

use App\Models\Centroid;
use App\Models\KmeansData;
use App\Models\KmeansDataReal;
use App\Models\Normalization;
use App\Models\TempCluster;
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
        $kmeans = new KmeansDataReal();

        $kmeans->fill($data);
        $kmeans->save();

        return $kmeans;
    }

    /**
     * Menyimpan data hasil transformasi
     * 
     * @param array $data
     */
    public function saveTransformation(array $data)
    {
        $transformation = new KmeansData();

        $transformation->fill($data);
        $transformation->save();

        return $transformation;
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
     * get list data
     * 
     * @param int $paginate
     * 
     * @return KmeansData
     */
    public function getListDataReals(int $paginate = 10, bool $isCount = false)
    {
        if (!$isCount) {
            return KmeansDataReal::orderBy('id')->paginate($paginate);
        }
        return KmeansDataReal::count();
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
    public function resetCluster(bool $isOptimization = true)
    {
        // hanya jika tidak optimisasi
        if ($isOptimization) {
            TempCluster::truncate();
        } else {
            Centroid::truncate();
        }
    }

    /**
     * get initial centroid
     * 
     * 
     */
    public function getInitialCentroid(int $countCentroid = null, bool $isOptimization = true)
    {
        $normalize = new Normalization();
        $banyakData = $normalize->count();
        $cluster = $countCentroid ?? (int)sqrt($banyakData / 2);

        $centroids = [];

        if ($isOptimization) {
            for ($index = 1; $index <= $cluster; $index++) {
                $tempCentroid = [];
                $data = $normalize->inRandomOrder()->take($index)->get();
                foreach ($data as $data) {
                    $tempCentroid[] = $data->getAttributes();
                }
                $centroids[] = $tempCentroid;
            }
        } else {
            $data = $normalize->inRandomOrder()->take($countCentroid)->get();
            foreach ($data as $data) {
                $centroids[] = $data->getAttributes();
            }
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
        $columnToNormalize = $this->columnToNormalize;
        $sum = 0;
        foreach ($data as $key => $value) {
            if (in_array($key, $columnToNormalize)) {
                $sum += pow($value - $centroid[$key], 2);
            }
        }
        return sqrt($sum);
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
     * Get list normalization
     * 
     */
    public function getListNormalization(int $limit = 10)
    {
        $normalize = Normalization::query()->with('data');

        $normalize = $normalize->paginate($limit);
        foreach ($normalize as $item) {
            $item->nama_pemilik = $item->data->nama_pemilik;
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

    public function getTempCluster()
    {
        return TempCluster::all();
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
                foreach ($centroids as $index => $value) {
                    $distance = $this->calculateDistance($value, $item);

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
            if ($oldCentroids === $centroids) {
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
     * mengetahui cluster terbaik
     * 
     */
    public function searchBestCluster(array $data, array $centroids, int $maxIterations = 100)
    {
        $iterations = 0;
        $clusters = [];
        $sumSSE = [];
        $sse = [];
        $dataClusters = [];
        $dataSSE = [];

        foreach ($centroids as $key => $centroid) {
            while ($iterations < $maxIterations) {
                $clusters[$key] = [];
                foreach ($data as $item) {
                    $minDistance = PHP_INT_MAX;
                    $closestCentroid = null;
                    foreach ($centroid as $index => $value) {
                        $distance = $this->calculateDistance($value, $item);

                        if ($distance < $minDistance) {
                            $minDistance = $distance;
                            $closestCentroid = $index;
                        }
                    }

                    $sumSSE[$key][$iterations][] = $minDistance;
                    $clusters[$key][$closestCentroid]['data'][] = $item;
                }
                $oldCentroids = $centroid;
                $centroid = $this->updateCentroids($clusters[$key]);

                if ($oldCentroids === $centroid) {
                    break;
                }
                $iterations++;
            }

            $sse[$key] = $this->calculateSSE($sumSSE[$key]);
        }

        $tempCluster = new TempCluster();
        foreach ($clusters as $index => $value) {
            $dataTemp = [
                'name_cluster' => ($index + 1) . ' cluster',
                'nilai_sse' => $sse[$index],
                'data_cluster' => []
            ];
            foreach ($value as $key => $data) {
                $dataTemp['data_cluster']['C' . ($key + 1)] = count($data['data']);
                $dataClusters['cluester ' . ($index + 1)]['C' . $key + 1] = count($data['data']);
                $dataSSE['Cluster ' . $key + 1] = $sse[$key];
            }
            $tempCluster->create($dataTemp);
        }

        return ['clusters' => $dataClusters, 'SSE' => $dataSSE];
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
     * Process label encoding
     * 
     */
    public function procesLabelEncoding()
    {
        $kmeansData = new KmeansDataReal();
        $columns = ['jenis_produksi', 'pendidikan', 'surat_izin', 'motif'];
        $mapping = [];

        // labeling
        foreach ($columns as $column) {
            $values = $kmeansData->pluck($column)->unique();
            $columnMapping = [];

            foreach ($values as $index => $value) {
                $columnMapping[$value] = $index + 1;
            }
            $mapping[$column] = $columnMapping;
        }

        $kmeansData->chunk(200, function ($items) use ($mapping, $columns) {
            foreach ($items as $item) {
                foreach ($columns as $column) {
                    $encodeValue = $mapping[$column][$item->{$column}];
                    $item->{$column} = $encodeValue;
                }
                $this->saveTransformation($item->toArray());
            }
        });

        return true;
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
        $data = KmeansDataReal::all();
        // hanya jika data tidak kosong
        if ($data->count() > 0) {
            KmeansDataReal::truncate();
            KmeansData::truncate();
            Centroid::truncate();
            Normalization::truncate();
            WeightAlternatif::truncate();
            TempCluster::truncate();
        }
    }
}
