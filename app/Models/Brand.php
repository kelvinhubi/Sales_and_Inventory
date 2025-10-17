<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    use HasFactory;

    // Relationship for past orders
    public function pastOrders()
    {
        return $this->hasMany(\App\Models\PastOrder::class);
    }
    protected $fillable = [
        'name',
        'description',
        'standard_items',
    ];

    protected $casts = [
        'standard_items' => 'array',
    ];
    /**
    * Get all branches for this brand
    */
    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    /**
    * Scope for searching brands
    */
    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%")
                    ->orWhereHas('branches', function ($q) use ($term) {
                        $q->where('name', 'like', "%{$term}%")
                          ->orWhere('address', 'like', "%{$term}%");
                    });
    }
}
