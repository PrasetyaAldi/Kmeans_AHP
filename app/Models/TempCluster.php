<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempCluster extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'data_cluster' => 'json'
    ];
}
