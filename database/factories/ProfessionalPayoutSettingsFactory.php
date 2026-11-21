<?php

namespace Database\Factories;

use App\Models\ProfessionalProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfessionalPayoutSettingsFactory extends Factory
{
    public function definition(): array
    {
        return [
            'professional_profile_id' => ProfessionalProfile::factory(),
            'payout_mode' => 'direct_pix',
            'pix_key_type' => 'email',
            'pix_key' => fake()->safeEmail(),
            'pix_holder_name' => fake()->name(),
            'pix_holder_document' => fake('pt_BR')->cpf(false),
            'custom_fee_percentage' => null,
            'fee_terms_accepted_at' => now(),
            'fee_terms_version' => '1.0',
            'is_payout_blocked' => false,
            'blocked_reason' => null,
            'blocked_at' => null,
            'blocked_by' => null,
            'created_by' => User::factory(),
            'updated_by' => fn (array $attributes) => $attributes['created_by'],
            'deleted_by' => null,
        ];
    }

    public function platformIntermediation(): static
    {
        return $this->state(fn () => ['payout_mode' => 'platform_intermediation']);
    }

    public function withCustomFee(float $percentage = 10.00): static
    {
        return $this->state(fn () => ['custom_fee_percentage' => $percentage]);
    }

    public function blocked(): static
    {
        return $this->state(fn () => [
            'is_payout_blocked' => true,
            'blocked_reason' => fake()->sentence(),
            'blocked_at' => now(),
            'blocked_by' => User::factory(),
        ]);
    }
}