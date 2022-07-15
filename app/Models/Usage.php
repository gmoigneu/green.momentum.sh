<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usage extends Model
{
    use HasFactory;

    protected $fillable = [
        'countryCode',
        'carbonIntensity',
        'fossilFuelPercentage',
        'units',
        'datacenter_id'
    ];

    /**
     * Get the datacenter for the usage.
     */
    public function datacenters(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Datacenter::class);
    }
}
