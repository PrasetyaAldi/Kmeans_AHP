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
        'eigen_value',
        'cr'
    ];

    /**
     * Get the normalize that owns the WeightAlternatif
     */
    public function normalize(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Normalization::class);
    }

    /**
     * Get the criteria that owns the WeightAlternatif
     */
    public function criteria(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Criteria::class);
    }

    /**
     * get the data that owns the weightAlternatif
     */
    public function data(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(KmeansData::class, 'normalize_id', 'id');
    }
}
