<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessLocation extends Model
{
    /** @use HasFactory<\Database\Factories\BusinessLocationFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_id',
        'name',
        'address',
        'latitude',
        'longitude'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
