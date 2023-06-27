<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeightAlternatif extends Model
{
    use HasFactory;

    protected $table = 'bobot_alternatif';

    protected $fillable = [
        'normalize_id',
        'criteria_id',
        'eigen_value'
    ];
}
