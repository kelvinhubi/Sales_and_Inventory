<?php

namespace Tests\Unit\Models;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_be_created()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 25.00,
            'quantity' => 100,
            'perishable' => 'no',
        ]);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('Test Product', $product->name);
        $this->assertEquals('25.00', $product->price);
        $this->assertEquals(100, $product->quantity);
    }

    public function test_product_has_correct_attributes()
    {
        $product = Product::create([
            'name' => 'Sample Item',
            'price' => 15.50,
            'quantity' => 50,
            'perishable' => 'yes',
        ]);

        $this->assertNotNull($product->id);
        $this->assertNotNull($product->created_at);
        $this->assertNotNull($product->updated_at);
    }

    public function test_product_low_stock_scope()
    {
        Product::create(['name' => 'Low Stock', 'price' => 10.00, 'quantity' => 5, 'perishable' => 'no']);
        Product::create(['name' => 'Normal Stock', 'price' => 15.00, 'quantity' => 50, 'perishable' => 'no']);

        $lowStockProducts = Product::lowStock()->get();

        $this->assertEquals(1, $lowStockProducts->count());
        $this->assertEquals('Low Stock', $lowStockProducts->first()->name);
    }

    public function test_product_out_of_stock_scope()
    {
        Product::create(['name' => 'In Stock', 'price' => 10.00, 'quantity' => 25, 'perishable' => 'no']);
        Product::create(['name' => 'Out of Stock', 'price' => 15.00, 'quantity' => 0, 'perishable' => 'no']);

        $outOfStockProducts = Product::outOfStock()->get();

        $this->assertEquals(1, $outOfStockProducts->count());
        $this->assertEquals('Out of Stock', $outOfStockProducts->first()->name);
    }
}
