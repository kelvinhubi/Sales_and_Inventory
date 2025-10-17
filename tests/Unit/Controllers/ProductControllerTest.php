<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\Api\ProductController;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_products()
    {
        // Create some test products
        Product::create([
            'name' => 'Test Product 1',
            'price' => 10.00,
            'quantity' => 50,
            'perishable' => 'no',
        ]);

        Product::create([
            'name' => 'Test Product 2',
            'price' => 20.00,
            'quantity' => 30,
            'perishable' => 'yes',
        ]);

        $controller = new ProductController();
        $request = new Request();

        $response = $controller->index($request);

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $data);
        $this->assertCount(2, $data['data']);
    }

    public function test_show_returns_single_product()
    {
        $product = Product::create([
            'name' => 'Single Product',
            'price' => 15.00,
            'quantity' => 40,
            'perishable' => 'no',
        ]);

        $controller = new ProductController();
        $response = $controller->show($product);

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $data);
        $this->assertEquals('Single Product', $data['data']['name']);
    }

    public function test_index_with_search_filter()
    {
        Product::create([
            'name' => 'Apple Juice',
            'price' => 5.00,
            'quantity' => 100,
            'perishable' => 'yes',
        ]);

        Product::create([
            'name' => 'Orange Juice',
            'price' => 6.00,
            'quantity' => 80,
            'perishable' => 'yes',
        ]);

        $controller = new ProductController();
        $request = new Request(['search' => 'Apple']);

        $response = $controller->index($request);

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertCount(1, $data['data']);
        $this->assertEquals('Apple Juice', $data['data'][0]['name']);
    }

    public function test_index_returns_empty_when_no_products()
    {
        $controller = new ProductController();
        $request = new Request();

        $response = $controller->index($request);

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $data);
        $this->assertEmpty($data['data']);
    }
}
