<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'unit',
        'price',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope a query to only include active items.
     * Cara pakai: MasterItem::active()->get();
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}