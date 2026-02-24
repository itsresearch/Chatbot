<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Website;

class WebsiteSeeder extends Seeder
{
    public function run()
    {
        Website::create([
            'name' => 'Test Website',
            'domain' => 'http://localhost',
            'api_key' => '123456'
        ]);
    }
}