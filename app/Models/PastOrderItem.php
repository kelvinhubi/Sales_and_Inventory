<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product; // Make sure this is at the top with other use statements
use App\Models\PastOrder;
class PastOrderItem extends Model
{
    use HasFactory;
    protected $fillable = ['past_order_id', 'product_id', 'quantity', 'price'];
    

public function product()
{
    return $this->belongsTo(Product::class);
}
// In app/Models/PastOrderItem.php

 // Make sure to add this import

public function pastOrder()
{
    return $this->belongsTo(PastOrder::class, 'past_order_id');
}
}