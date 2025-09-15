<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RejectedGoodItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'rejected_good_id',
        'product_id',
        'quantity',
    ];

    public function rejectedGood()
    {
        return $this->belongsTo(RejectedGood::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}