<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ApiAccessLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'portal' => 'student_app',
            'actor_type' => 'student',
            'actor_id' => fake()->numberBetween(1, 1000),
            'method' => fake()->randomElement(['GET', 'POST', 'PUT', 'PATCH', 'DELETE']),
            'endpoint' => '/api/v1/'.fake()->slug(2),
            'status_code' => fake()->randomElement([200, 201, 204, 400, 401, 403, 404, 422, 500]),
            'response_time_ms' => fake()->numberBetween(20, 2000),
            'ip_address' => fake()->ipv4(),
            'created_at' => now(),
        ];
    }
}