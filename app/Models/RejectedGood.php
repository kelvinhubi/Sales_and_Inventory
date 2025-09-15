<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RejectedGood extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'brand_id',
        'branch_id',
        'dr_no',
        'amount',
        'reason',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function items()
    {
        return $this->hasMany(RejectedGoodItem::class);
    }
}