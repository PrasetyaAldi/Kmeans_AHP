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
}
