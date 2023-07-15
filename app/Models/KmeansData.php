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

    protected $guarded = ['id'];
    protected $casts = [
        'jumlah_pekerja' => 'integer',
        'jenis_produksi' => 'integer',
        'kapasitas_produksi' => 'integer',
        'harga_satuan' => 'integer',
        'nilai_produksi' => 'integer',
        'nilai_investasi' => 'integer',
        'umur' => 'integer',
        'pendidikan' => 'integer',
        'surat_izin' => 'integer',
        'motif' => 'integer',
    ];

    /**
     * get the normalized data of the data
     */
    public function normalizedData(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Normalization::class);
    }

    /**
     * get the centroid of the data
     */
    public function centroid(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Centroid::class);
    }

    /**
     * get the cluster of the data
     */
    public function cluster(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Cluster::class);
    }
}
