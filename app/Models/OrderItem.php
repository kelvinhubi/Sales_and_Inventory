<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'name',
        'quantity',
        'price',
        'product_id'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}