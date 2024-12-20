<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    /** @use HasFactory<\Database\Factories\ItemFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_type_id',
        'serial_number',
        'status',
        'business_location_id',
        'warehouse_id',
        'storage_location',
        'purchase_date',
        'purchase_price',
        'lease_start_date',
        'lease_end_date'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'lease_start_date' => 'date',
        'lease_end_date' => 'date',
        'purchase_price' => 'decimal:2'
    ];

    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function businessLocation()
    {
        return $this->belongsTo(BusinessLocation::class);
    }

    public function movements()
    {
        return $this->hasMany(ItemMovement::class);
    }
}
