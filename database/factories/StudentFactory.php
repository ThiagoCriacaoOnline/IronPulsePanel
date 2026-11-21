<?php

namespace Database\Factories;

use App\Models\ProfessionalProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class StudentFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'cpf' => fake('pt_BR')->unique()->cpf(),
            'phone' => fake('pt_BR')->cellphone(false),
            'email_verified_at' => now(),
            'invite_activated_at' => null,
            'avatar' => null,
            'path_avatar' => null,
            'birth_date' => fake()->dateTimeBetween('-60 years', '-16 years')->format('Y-m-d'),
            'gender' => fake()->randomElement(['male', 'female', 'other', 'prefer_not_to_say']),
            'goal' => fake()->randomElement(['hypertrophy', 'weight_loss', 'conditioning', 'health', 'rehabilitation', 'sport_performance']),
            'average_rating' => 0,
            'ratings_count' => 0,
            'registration_source' => 'app_self_signup',
            'registered_by_professional_id' => null,
            'terms_accepted_at' => now(),
            'terms_version' => '1.0',
            'is_active' => true,
            'blocked_reason' => null,
            'blocked_at' => null,
            'blocked_by' => null,
            'created_by' => User::factory(),
            'updated_by' => fn (array $attributes) => $attributes['created_by'],
            'deleted_by' => null,
        ];
    }

    public function invitedBy(?ProfessionalProfile $professional = null): static
    {
        return $this->state(fn () => [
            'registration_source' => 'invite_code',
            'registered_by_professional_id' => $professional?->id ?? ProfessionalProfile::factory(),
            'invite_activated_at' => now(),
        ]);
    }

    public function blocked(): static
    {
        return $this->state(fn () => [
            'is_active' => false,
            'blocked_reason' => fake()->sentence(),
            'blocked_at' => now(),
            'blocked_by' => User::factory(),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}