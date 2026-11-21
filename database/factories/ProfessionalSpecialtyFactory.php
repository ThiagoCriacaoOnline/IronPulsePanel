<?php

namespace Database\Factories;

use App\Models\ProfessionalProfile;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfessionalSpecialtyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'professional_profile_id' => ProfessionalProfile::factory(),
            'specialty_id' => Specialty::factory(),
            'created_at' => now(),
            'created_by' => User::factory(),
            'deleted_at' => null,
            'deleted_by' => null,
        ];
    }

    public function deleted(): static
    {
        return $this->state(fn () => [
            'deleted_at' => now(),
            'deleted_by' => User::factory(),
        ]);
    }
}