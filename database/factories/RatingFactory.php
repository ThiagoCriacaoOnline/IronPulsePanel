<?php

namespace Database\Factories;

use App\Models\ProfessionalStudent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RatingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'professional_student_id' => ProfessionalStudent::factory(),
            'reviewer_type' => 'student',
            'reviewer_id' => fn (array $attributes) => ProfessionalStudent::findOrFail($attributes['professional_student_id'])->student_id,
            'reviewed_type' => 'professional',
            'reviewed_id' => fn (array $attributes) => ProfessionalStudent::findOrFail($attributes['professional_student_id'])->professional_profile_id,
            'score' => fake()->numberBetween(1, 5),
            'comment' => fake()->sentence(),
            'tags' => [],
            'moderation_status' => 'published',
            'moderated_at' => null,
            'moderated_by' => null,
            'moderation_reason' => null,
            'created_by' => User::factory(),
            'updated_by' => fn (array $attributes) => $attributes['created_by'],
            'deleted_by' => null,
        ];
    }

    public function byProfessional(): static
    {
        return $this->state(fn (array $attributes) => [
            'reviewer_type' => 'professional',
            'reviewer_id' => ProfessionalStudent::findOrFail($attributes['professional_student_id'])->professional_profile_id,
            'reviewed_type' => 'student',
            'reviewed_id' => ProfessionalStudent::findOrFail($attributes['professional_student_id'])->student_id,
        ]);
    }

    public function reported(): static
    {
        return $this->state(fn () => ['moderation_status' => 'reported']);
    }

    public function hidden(): static
    {
        return $this->state(fn () => [
            'moderation_status' => 'hidden_by_moderation',
            'moderated_at' => now(),
            'moderated_by' => User::factory(),
            'moderation_reason' => fake()->sentence(),
        ]);
    }
}