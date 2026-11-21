<?php

namespace Database\Factories;

use App\Models\ProfessionalProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfessionalContactFactory extends Factory
{
    public function definition(): array
    {
        return [
            'professional_profile_id' => ProfessionalProfile::factory(),
            'whatsapp' => fake('pt_BR')->cellphone(false),
            'phone' => fake('pt_BR')->cellphone(false),
            'public_email' => fake()->safeEmail(),
            'instagram' => '@'.fake()->userName(),
            'twitter' => '@'.fake()->userName(),
            'linkedin' => fake()->userName(),
            'tiktok' => '@'.fake()->userName(),
            'kawai' => fake()->userName(),
            'facebook' => fake()->userName(),
            'website' => fake()->url(),
            'city' => fake('pt_BR')->city(),
            'state' => fake('pt_BR')->stateAbbr(),
            'attendance_mode' => 'online',
            'created_by' => User::factory(),
            'updated_by' => fn (array $attributes) => $attributes['created_by'],
            'deleted_by' => null,
        ];
    }

    public function inPerson(): static
    {
        return $this->state(fn () => ['attendance_mode' => 'in_person']);
    }

    public function both(): static
    {
        return $this->state(fn () => ['attendance_mode' => 'both']);
    }
}