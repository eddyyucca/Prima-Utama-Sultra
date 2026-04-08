<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@pus.co.id'],
            [
                'name' => 'Administrator PUS',
                'email' => 'admin@pus.co.id',
                'password' => Hash::make('admin123'),
                'role' => 'superadmin',
            ]
        );
    }
}
