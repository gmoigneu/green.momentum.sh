<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code'];

    /**
     * Get the datacenters for the provider.
     */
    public function datacenters(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Datacenter::class);
    }
}
