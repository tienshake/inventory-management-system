<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Manufacturer extends Model
{

    protected $fillable = [
        'name',
        'slug',
        'description'
    ];


    public function productTypes(): HasMany
    {
        return $this->hasMany(ProductType::class);
    }
}
