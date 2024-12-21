<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{

    protected $fillable = [
        'name',
        'slug',
        'description'
    ];


    public function subCategories(): HasMany
    {
        return $this->hasMany(SubCategory::class);
    }

    public function productTypes(): HasMany
    {
        return $this->hasMany(ProductType::class);
    }
}
