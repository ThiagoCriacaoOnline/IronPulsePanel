<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PermissionFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => Str::title($name),
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'module' => fake()->randomElement(['users', 'roles', 'permissions', 'reports']),
            'is_sensitive' => false,
            'is_active' => true,
            'created_by' => User::factory(),
            'updated_by' => fn (array $attributes) => $attributes['created_by'],
            'deleted_by' => null,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function sensitive(): static
    {
        return $this->state(fn () => ['is_sensitive' => true]);
    }

    public function deleted(): static
    {
        return $this->state(fn () => [
            'deleted_at' => now(),
            'deleted_by' => User::factory(),
        ]);
    }
}
