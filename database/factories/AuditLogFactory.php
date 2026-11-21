<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuditLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'actor_type' => 'backoffice_user',
            'actor_id' => fake()->numberBetween(1, 1000),
            'actor_name' => fake()->name(),
            'portal' => 'backoffice',
            'action' => fake()->randomElement(['created', 'updated', 'deleted']),
            'auditable_type' => Role::class,
            'auditable_id' => fake()->numberBetween(1, 1000),
            'old_values' => [],
            'new_values' => [],
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'request_url' => fake()->url(),
            'request_method' => 'POST',
            'severity' => 'info',
            'created_at' => now(),
        ];
    }

    public function warning(): static
    {
        return $this->state(fn () => ['severity' => 'warning']);
    }

    public function critical(): static
    {
        return $this->state(fn () => ['severity' => 'critical']);
    }
}