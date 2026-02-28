<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@chatbot.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'company_name' => 'Platform Owner',
                'is_active' => true,
            ]
        );

        $this->command->info('Super Admin created: admin@chatbot.com / password');
    }
}
