<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],

    // SECURITY: Replace with your actual domain(s) in production
    'allowed_origins' => [
        env('APP_URL', 'http://localhost'),
        'http://localhost:8000',
        'http://127.0.0.1:8000',
        // Add your production domains here:
        'https://sales-and-inventory.great-site.net/',
        'https://www.sales-and-inventory.great-site.net/',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Content-Type', 'X-Requested-With', 'Authorization', 'X-CSRF-TOKEN', 'Accept'],

    'exposed_headers' => [],

    'max_age' => 3600,

    'supports_credentials' => true,

];
