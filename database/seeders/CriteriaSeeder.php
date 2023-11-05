<?php

namespace Database\Seeders;

use App\Models\Criteria;
use Illuminate\Database\Seeder;

class CriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'jumlah_pekerja',
            'kapasitas_produksi',
            'nilai_produksi',
            'nilai_investasi',
            'surat_izin',
        ];
        foreach ($data as $item) {
            Criteria::create([
                'name' => $item
            ]);
        }
    }
}
