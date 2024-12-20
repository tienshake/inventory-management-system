<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemMovement extends Model
{
    /** @use HasFactory<\Database\Factories\ItemMovementFactory> */
    use HasFactory;

    protected $fillable = [
        'item_id',
        'from_location_type',
        'from_location_id',
        'to_location_type',
        'to_location_id',
        'movement_date',
        'notes'
    ];

    protected $casts = [
        'movement_date' => 'date'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function fromLocation()
    {
        return $this->morphTo('from_location');
    }

    public function toLocation()
    {
        return $this->morphTo('to_location');
    }
}
