<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SystemSettingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'group' => 'general',
            'key' => Str::snake(fake()->unique()->words(3, true)),
            'value' => fake()->word(),
            'value_type' => 'string',
            'description' => fake()->sentence(),
            'is_public' => false,
            'updated_by' => User::factory(),
        ];
    }

    public function public(): static
    {
        return $this->state(fn () => ['is_public' => true]);
    }

    public function boolean(bool $value = true): static
    {
        return $this->state(fn () => [
            'value' => $value ? '1' : '0',
            'value_type' => 'boolean',
        ]);
    }

    public function integer(int $value = 10): static
    {
        return $this->state(fn () => [
            'value' => (string) $value,
            'value_type' => 'integer',
        ]);
    }
}