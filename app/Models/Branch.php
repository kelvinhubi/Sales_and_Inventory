<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Branch extends Model
{
    use HasFactory;
    use HasFactory;

    // Relationship for past orders
    public function pastOrders()
    {
        return $this->hasMany(\App\Models\PastOrder::class);
    }
    protected $fillable = [
       'brand_id',
       'name',
       'address',
       'contact_person',
       'contact_number',
       'status',
    ];

    protected $appends = ['last_order_date'];

    public function getLastOrderDateAttribute()
    {
        return $this->orders()->latest()->first()?->created_at;
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    /**
     * Get the brand that owns this branch
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Scope for searching branches
     */
    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'like', "%{$term}%")
                    ->orWhere('address', 'like', "%{$term}%")
                    ->orWhere('contact_person', 'like', "%{$term}%")
                    ->orWhere('contact_number', 'like', "%{$term}%");
    }


}
