<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'dob' => '2000-01-01',                  // example date of birth
            'phone' => '1234567890',
            'phone_country_code' => '+234',
            'location_country_code' => 'NG',
            'location' => 'Lagos',
            'gender' => 'male',
            'role' => 'student',
            'password' => bcrypt('password123'),    // encrypt the password
        ]);
    }
}
