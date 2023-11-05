<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KmeansDataReal extends Model
{
    use HasFactory;

    protected $table = 'kmeans_data_reals';

    protected $fillable = [
        'nama_pemilik',
        'jumlah_pekerja',
        'kapasitas_produksi',
        'nilai_produksi',
        'nilai_investasi',
        'surat_izin',
    ];
}
