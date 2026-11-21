<?php

namespace Database\Factories;

use App\Models\ProfessionalProfile;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfessionalStudentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'professional_profile_id' => ProfessionalProfile::factory(),
            'student_id' => Student::factory(),
            'service_type' => 'training',
            'status' => 'active',
            'started_at' => now(),
            'ended_at' => null,
            'ended_by_type' => null,
            'ended_by_id' => null,
            'end_reason' => null,
            'created_by' => User::factory(),
            'updated_by' => fn (array $attributes) => $attributes['created_by'],
            'deleted_by' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending', 'started_at' => null]);
    }

    public function paused(): static
    {
        return $this->state(fn () => ['status' => 'paused']);
    }

    public function ended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'ended',
            'ended_at' => now(),
            'ended_by_type' => 'student',
            'ended_by_id' => $attributes['student_id'],
            'end_reason' => fake()->sentence(),
        ]);
    }

    public function nutrition(): static
    {
        return $this->state(fn () => ['service_type' => 'nutrition']);
    }

    public function both(): static
    {
        return $this->state(fn () => ['service_type' => 'both']);
    }
}