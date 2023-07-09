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
        foreach ($data as $item) {
            Criteria::create([
                'name' => $item
            ]);
        }
    }
}
