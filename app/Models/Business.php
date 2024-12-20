<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Business extends Model
{
    /** @use HasFactory<\Database\Factories\BusinessFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'organization_number',
        'email',
        'phone',
        'business_type'
    ];

    public function locations()
    {
        return $this->hasMany(BusinessLocation::class);
    }

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }

    public function items(): HasManyThrough
    {
        return $this->hasManyThrough(
            Item::class,
            BusinessLocation::class,
            'business_id', // Foreign key on business_locations table
            'business_location_id', // Foreign key on items table
            'id', // Local key on businesses table
            'id' // Local key on business_locations table
        );
    }
}
