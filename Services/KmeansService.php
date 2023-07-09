<?php

namespace Services;

use App\Models\Centroid;
use App\Models\KmeansData;
use App\Models\Normalization;

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
    public function getListData(int $paginate = 10)
    {
        return KmeansData::orderBy('id')->paginate($paginate);
    }

    /**
     * Normalization
     * 
     */
    public function normalizationData()
    {
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
                    $data[$column] = $this->normalize($value, $column);
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
        $normalize = $normalize->whereIn('data_id', [4, 8, 13])->get();
        // $normalize = $normalize->inRandomOrder()->take(3)->get();

        $centroid = [];
        foreach ($normalize as $index => $data) {
            $centroid[] = $data->getAttributes();
        }

        return $centroid;
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
        return Normalization::all()->toArray();
    }

    /**
     * Mendapatkan list cluster
     * 
     * @param int $paginare
     */
    public function getCluster(int $paginate = 10)
    {
        $cluster = Centroid::selectRaw('cluster, COUNT(1) as jumlah ')->groupBy('cluster')->orderBy('cluster');
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

        $sse = $this->calculateSSE($clusters, $centroids);


        // menyimpan ke database
        $centroidModel = new Centroid();
        foreach ($clusters as $key => $value) {
            foreach ($value['data'] as $item) {
                $centroidModel->create([
                    'normalize_id' => $item['id'],
                    'cluster' => 'C' . $key + 1,
                ]);
            }
        }

        return ['clusters' => $clusters, 'sse' => $sse];
    }

    /**
     * calculate SSE
     * 
     * @param array $clusters
     * @param array $centroids
     * 
     */
    public function calculateSSE(array $clusters, array $centroids)
    {
        $sse = 0;
        $columnToNormalize = $this->columnToNormalize;
        foreach ($clusters as $key => $value) {
            foreach ($value['data'] as $item) {
                $item = array_intersect_key($item, array_flip($columnToNormalize));
                $sse += pow($this->calculateDistance($centroids[$key], $item), 2);
            }
        }

        return $sse;
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
}
