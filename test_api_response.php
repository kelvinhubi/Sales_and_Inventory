<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();

use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;

$request = new Request();
$request->merge(['per_page' => 3]);

$controller = new ProductController();
$response = $controller->index($request);

echo "API Response:\n";
echo json_encode($response->getData(true), JSON_PRETTY_PRINT);