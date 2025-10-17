<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PastOrder extends Model
{
    use HasFactory;
    protected $fillable = ['dr_number', 'brand_id', 'branch_id', 'total_amount'];

    public function items(): HasMany
    {
        return $this->hasMany(PastOrderItem::class, 'past_order_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
