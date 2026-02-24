<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Website;

class TestWebsiteSeeder extends Seeder
{
    public function run()
    {
        Website::firstOrCreate(
            ['api_key' => '123456'],
            ['name' => 'Test Website']
        );
        
        echo "✓ Test website created with API key: 123456\n";
    }
}
