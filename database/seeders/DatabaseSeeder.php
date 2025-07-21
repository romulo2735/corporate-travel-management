<?php

namespace Database\Seeders;

use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->createMany([
            ['name' => 'Jonh Doe', 'email' => 'jonh@example.com'],
            ['name' => 'Jane Doe', 'email' => 'jane@example.com']
        ]);
    }
}
