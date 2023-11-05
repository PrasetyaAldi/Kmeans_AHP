<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Normalization extends Model
{
    use HasFactory;

    protected $table = 'kmeans_normalized_data';

    protected $fillable = [
        'data-id',
        'jumlah_pekerja',
        'kapasitas_produksi',
        'nilai_produksi',
        'nilai_investasi',
        'surat_izin',
    ];

    /**
     * get the centroid of the data
     */
    public function centroid(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Centroid::class);
    }

    /**
     * get the data of the normalized data
     */
    public function data(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(KmeansData::class);
    }
}
