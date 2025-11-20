<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\SuperAdmin;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        SuperAdmin::create([
            'username' => 'admin',
            'email' => 'admin@admissionportal.com',
            'password' => Hash::make('admin123'),
            'name' => 'Super Administrator',
            'is_active' => true,
        ]);
    }
}