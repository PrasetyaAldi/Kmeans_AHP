<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Criteria extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function weightCriteria()
    {
        return $this->hasOne(WeightCriteria::class, 'criteria_id', 'id');
    }

    /**
     * Get all of the weightAlternatifs for the Criteria
     */
    public function weightAlternatifs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(WeightAlternatif::class, 'criteria_id', 'id');
    }
}
