<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_type_id',
        'serial_number',
        'status',
        'business_id',
        'location_id',
        'warehouse_id',
        'storage_location',
        'date_acquired',
        'cost_price',
        'lease_start',
        'lease_end',
    ];

    protected $casts = [
        'date_acquired' => 'date',
        'lease_start' => 'date',
        'lease_end' => 'date',
        'cost_price' => 'decimal:2',
    ];

    public function productType(): BelongsTo
    {
        return $this->belongsTo(ProductType::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}
