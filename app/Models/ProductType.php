<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductType extends Model
{
    /** @use HasFactory<\Database\Factories\ProductTypeFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'manufacturer',
        'model_number',
        'category',
        'subcategory'
    ];

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
