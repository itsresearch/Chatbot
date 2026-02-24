<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

use App\Models\Website;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$request = \Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

// Create website if it doesn't exist
$website = Website::firstOrCreate(
    ['api_key' => '123456'],
    ['name' => 'Test Website']
);

echo "Website created/retrieved successfully!\n";
echo "Website ID: {$website->id}\n";
echo "API Key: {$website->api_key}\n";
echo "Name: {$website->name}\n";
