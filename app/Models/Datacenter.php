<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Datacenter extends Model
{
    use HasFactory;

    /**
     * Get the provider that owns the datacenter.
     */
    public function provider(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    /**
     * Get the regions for the datacenter.
     */
    public function regions(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Region::class);
    }

    /**
     * Get the usages
     */
    public function usages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Usage::class)->orderBy('created_at', 'DESC')->limit(10);
    }
}
