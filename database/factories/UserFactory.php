<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'dob' => $this->faker->date(),
            'phone' => $this->faker->phoneNumber(),
            'phone_country_code' => '+234',
            'location_country_code' => 'NG',
            'location' => $this->faker->city(),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'role' => $this->faker->randomElement(['student', 'admin']),
            'password' => bcrypt('password123'), // default password
            'remember_token' => Str::random(10),
        ];
    }
}
