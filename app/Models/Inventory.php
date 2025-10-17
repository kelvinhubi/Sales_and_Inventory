<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;
    protected $table = 'inventory';
    protected $fillable = [
        'product_id',
        'quantity',
    ];
    protected $casts = [
        'quantity' => 'integer',
    ];
    public function product()
    {
        // Each inventory entry belongs to a single product
        return $this->belongsTo(Product::class, 'product_id');
    }
}
