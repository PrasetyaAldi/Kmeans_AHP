<?php

namespace Tests\Unit\Services;

use App\Models\KmeansData;
use App\Models\Normalization;
use Services\AHPService;
use Services\KmeansService;
use Tests\TestCase;

class AHPServiceTest extends TestCase
{
    /**
     * Seeding Database
     */
    public function seedingDatabase()
    {
        $this->artisan('migrate:fresh');
        $this->seed(['CriteriaSeeder']);
    }

    /**
     * normalization data in Kmeans
     */
    public function processKmeans()
    {
        $this->seed(['KmeansDataSeeder']);
        $kMeansService = new KmeansService;
        $kMeansService->normalizationData();
        $data = Normalization::all()->toArray();
        $centroid = $kMeansService->getInitialCentroid();

        $kMeansService->processKmeans($data, $centroid);
    }

    /** @test */
    public function get_criteria_weight_return_response_success()
    {
        //Arrange | Given
        $this->seedingDatabase();
        $this->processKmeans();

        $ahpService = new AHPService;

        $criteria = [
            ['id' => 1, 'penilaian' => [1.00, 2.00, 3.00, 1.00, 2.00, 1.00, 1.00, 1.00, 1.00, 1.00]],
            ['id' => 2, 'penilaian' => [0.50, 1.00, 2.00, 1.00, 1.00, 1.00, 1.00, 3.00, 1.00, 1.00]],
            ['id' => 3, 'penilaian' => [0.33, 0.50, 1.00, 1.00, 1.00, 1.00, 1.00, 3.00, 1.00, 1.00]],
            ['id' => 4, 'penilaian' => [1.00, 1.00, 1.00, 1.00, 1.00, 3.00, 1.00, 1.00, 1.00, 1.00]],
            ['id' => 5, 'penilaian' => [0.50, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 3.00, 1.00, 1.00]],
            ['id' => 6, 'penilaian' => [1.00, 1.00, 1.00, 0.33, 1.00, 1.00, 2.00, 1.00, 3.00, 1.00]],
            ['id' => 7, 'penilaian' => [1.00, 1.00, 1.00, 1.00, 1.00, 0.50, 1.00, 1.00, 1.00, 1.00]],
            ['id' => 8, 'penilaian' => [1.00, 0.33, 0.33, 1.00, 0.33, 1.00, 1.00, 1.00, 2.00, 1.00]],
            ['id' => 9, 'penilaian' => [1.00, 1.00, 1.00, 1.00, 1.00, 0.33, 1.00, 0.50, 1.00, 1.00]],
            ['id' => 10, 'penilaian' => [1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00]]
        ];


        //Act | When
        $result = $ahpService->getCriteriaWeight($criteria);

        //Assert | Then
        $this->assertEquals($result, $result);
    }

    /** @test */
    public function get_weight_alternatif_return_success()
    {
        //Arrange | Given
        $this->seedingDatabase();
        $this->processKmeans();
        $ahpService = new AHPService;

        $data = [
            'criteria_id' => 1,
            'data' => [
                [1, 3, 1, 1, 2, 1, 2, 3],
                [0.3333, 1, 3, 1, 2, 1, 2, 5],
                [1, 0.3333, 1, 3, 2, 2, 1, 3],
                [1, 1, 0.3333, 1, 5, 2, 4, 2],
                [0.5, 0.5, 0.5, 0.2, 1, 3, 2, 1],
                [1, 1, 0.5, 0.5, 0.3333, 1, 1, 2],
                [0.5, 0.5, 1, 0.25, 0.5, 1, 1, 3],
                [0.3333, 0.2, 0.3333, 0.5, 1, 0.5, 0.3333, 1],
            ]
        ];

        //Act | When
        $result = $ahpService->getAlternatifWeight($data['data'], $data['criteria_id']);

        //Assert | Then

    }
}
