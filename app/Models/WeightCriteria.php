<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeightCriteria extends Model
{
    use HasFactory;

    protected $table = 'bobot_criteria';

    protected $fillable = [
        'criteria_id',
        'eigen_value',
        'bobot',
        'created_at',
        'updated_at',
    ];

    public function criteria()
    {
        return $this->belongsTo(Criteria::class, 'criteria_id', 'id');
    }
}
