<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Centroid extends Model
{
    use HasFactory;

    protected $table = 'kmeans_centroids';

    protected $fillable = [
        'normalize_id',
        'cluster',
        'nilai_sse'
    ];

    /**
     * Get the normalize that owns the Centroid
     */
    public function normalize(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Normalization::class);
    }

    /**
     * get the data that owns the centroid
     *
     */
    public function data(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(KmeansData::class, 'normalize_id', 'id');
    }
}
