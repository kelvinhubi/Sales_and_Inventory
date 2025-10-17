<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Make sure this is at the top with other use statements
class PastOrderItem extends Model
{
    use HasFactory;
    protected $fillable = ['past_order_id', 'product_id', 'quantity', 'price'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function pastOrder()
    {
        return $this->belongsTo(PastOrder::class, 'past_order_id');
    }
}
