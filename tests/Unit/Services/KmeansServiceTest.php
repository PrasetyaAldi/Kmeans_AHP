<?php

namespace Tests\Unit\Services;

use App\Models\KmeansData;
use App\Models\Normalization;
use App\Models\User;
use Database\Seeders\KmeansDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Services\KmeansService;
use Tests\TestCase;

class KmeansServiceTest extends TestCase
{

    /**
     * seeding database
     * 
     */
    protected function seedDatabase()
    {
        $this->artisan('migrate:fresh');
        $this->artisan('db:seed', ['--class' => KmeansDataSeeder::class]);
    }

    /** @test */
    public function normalization_data_return_response_success()
    {
        $this->seedDatabase();
        //Arrange | Given
        $kmeansService = new KmeansService;
        $kMeansData = KmeansData::count();

        //Act | When
        $kmeansService->normalizationData();

        //Assert | Then
        $this->assertEquals($kMeansData, $kMeansData);
    }

    /** @test */
    public function get_initial_centroid_return_response_success()
    {
        $this->seedDatabase();
        //Arrange | Given
        $kmeansService = new KmeansService;

        //Act | When
        $kmeansService->normalizationData();
        $data = Normalization::all()->toArray();

        $centroid = $kmeansService->getInitialCentroid();

        $test = $kmeansService->processKmeans($data, $centroid);
        foreach ($test as $key => $value) {
            $item['cluster-' . $key] = $value;
        }

        dd($item);
        //Assert | Then
    }
}
