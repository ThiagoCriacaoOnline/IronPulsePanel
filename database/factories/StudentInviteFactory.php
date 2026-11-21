<?php

namespace Database\Factories;

use App\Models\ProfessionalProfile;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class StudentInviteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'professional_profile_id' => ProfessionalProfile::factory(),
            'student_id' => null,
            'service_type' => 'training',
            'code' => Str::upper(Str::random(12)),
            'invite_type' => 'single_student',
            'max_uses' => 1,
            'uses_count' => 0,
            'expires_at' => now()->addDays(7),
            'status' => 'pending',
            'used_at' => null,
            'used_ip' => null,
            'revoked_at' => null,
            'revoked_by_type' => null,
            'revoked_by_id' => null,
            'revoke_reason' => null,
            'created_by' => User::factory(),
            'updated_by' => fn (array $attributes) => $attributes['created_by'],
            'deleted_by' => null,
        ];
    }

    public function openLink(int $maxUses = 10): static
    {
        return $this->state(fn () => [
            'invite_type' => 'open_link',
            'max_uses' => $maxUses,
        ]);
    }

    public function used(): static
    {
        return $this->state(fn () => [
            'student_id' => Student::factory(),
            'status' => 'used',
            'uses_count' => 1,
            'used_at' => now(),
            'used_ip' => fake()->ipv4(),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'status' => 'expired',
            'expires_at' => now()->subDay(),
        ]);
    }

    public function revoked(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'revoked',
            'revoked_at' => now(),
            'revoked_by_type' => 'professional',
            'revoked_by_id' => $attributes['professional_profile_id'],
            'revoke_reason' => fake()->sentence(),
        ]);
    }
}