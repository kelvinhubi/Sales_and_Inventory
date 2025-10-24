<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Product extends Model
{
    use HasFactory;

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // Clear AI insights cache when products are created, updated, or deleted
        static::created(function () {
            self::clearAIInsightsCache();
        });

        static::updated(function () {
            self::clearAIInsightsCache();
        });

        static::deleted(function () {
            self::clearAIInsightsCache();
        });
    }

    /**
     * Clear AI insights cache
     */
    private static function clearAIInsightsCache()
    {
        // Clear all AI insights cache keys for different hours
        $today = now();
        for ($i = 0; $i < 24; $i++) {
            $cacheKey = 'ai_business_insights_' . $today->copy()->addHours($i)->format('Y-m-d-H');
            Cache::forget($cacheKey);
        }

        // Also clear current hour cache
        $currentCacheKey = 'ai_business_insights_' . $today->format('Y-m-d-H');
        Cache::forget($currentCacheKey);
    }

    // Relationship for rejected good items
    public function rejectedGoodItems()
    {
        return $this->hasMany(\App\Models\RejectedGoodItem::class);
    }

    protected $fillable = [
        'name',
        'category',
        'price',
        'original_price',
        'quantity',
        'perishable',
        'expiration_date',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'quantity' => 'integer',
        'expiration_date' => 'date',
    ];

    public function inventory()
    {
        return $this->hasOne(\App\Models\Inventory::class, 'product_id');
    }

    // Scope for low stock products
    public function scopeLowStock($query)
    {
        return $query->where('quantity', '>', 0)->where('quantity', '<=', 10);
    }

    // Scope for out of stock products
    public function scopeOutOfStock($query)
    {
        return $query->where('quantity', 0);
    }

    // Scope for perishable products
    public function scopePerishable($query, $perishable)
    {
        return $query->where('perishable', $perishable);
    }

    // Check if product has expired
    /*
    public function hasExpired()
    {
        if (!$this->expiration_date) {
            return false;
        }

        return $this->expiration_date->isPast();
    }

    // Scope for expired products
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiration_date')
                     ->where('expiration_date', '<', now());
    }*/
}
