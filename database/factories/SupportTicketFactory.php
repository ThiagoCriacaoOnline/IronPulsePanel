<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupportTicketFactory extends Factory
{
    public function definition(): array
    {
        return [
            'protocol_number' => 'TKT-'.now()->format('Y').'-'.fake()->unique()->numerify('######'),
            'requester_type' => 'student',
            'requester_id' => Student::factory(),
            'related_professional_id' => null,
            'related_student_id' => null,
            'category' => 'technical_bug',
            'priority' => 'medium',
            'status' => 'open',
            'subject' => fake()->sentence(6),
            'description' => fake()->paragraph(),
            'assigned_to' => null,
            'resolved_at' => null,
            'resolved_by' => null,
            'resolution_notes' => null,
            'created_by' => User::factory(),
            'updated_by' => fn (array $attributes) => $attributes['created_by'],
            'deleted_by' => null,
        ];
    }

    public function inProgress(): static
    {
        return $this->state(fn () => [
            'status' => 'in_progress',
            'assigned_to' => User::factory(),
        ]);
    }

    public function urgent(): static
    {
        return $this->state(fn () => ['priority' => 'urgent']);
    }

    public function resolved(): static
    {
        return $this->state(fn () => [
            'status' => 'resolved',
            'assigned_to' => User::factory(),
            'resolved_at' => now(),
            'resolved_by' => User::factory(),
            'resolution_notes' => fake()->sentence(),
        ]);
    }
}