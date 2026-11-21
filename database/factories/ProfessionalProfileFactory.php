<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class ProfessionalProfileFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'cpf_cnpj' => fake('pt_BR')->unique()->cpf(false),
            'email_verified_at' => now(),
            'avatar' => null,
            'path_avatar' => null,
            'profession_type' => 'nutritionist',
            'council_type' => 'crn',
            'council_number' => fake()->unique()->numerify('######'),
            'council_state' => fake('pt_BR')->stateAbbr(),
            'verification_status' => 'pending',
            'verified_at' => null,
            'verified_by' => null,
            'rejection_reason' => null,
            'average_rating' => 0,
            'ratings_count' => 0,
            'bio' => fake()->paragraph(),
            'is_accepting_students' => true,
            'is_active' => true,
            'created_by' => User::factory(),
            'updated_by' => fn (array $attributes) => $attributes['created_by'],
            'deleted_by' => null,
        ];
    }

    public function personal(): static
    {
        return $this->state(fn () => [
            'profession_type' => 'personal',
            'council_type' => 'cref',
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'verification_status' => 'approved',
            'verified_at' => now(),
            'verified_by' => User::factory(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'verification_status' => 'rejected',
            'verified_at' => now(),
            'verified_by' => User::factory(),
            'rejection_reason' => fake()->sentence(),
        ]);
    }

    public function notAcceptingStudents(): static
    {
        return $this->state(fn () => ['is_accepting_students' => false]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}