<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KmeansData extends Model
{
    use HasFactory;

    protected $table = 'kmeans_data';

    protected $fillable = [
        'nama_pemilik',
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
     * get the normalized data of the data
     */
    public function normalizedData(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Normalization::class);
    }
}
