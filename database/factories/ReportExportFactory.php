<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ReportExportFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'report_type' => 'students',
            'filters_used' => [],
            'file_format' => 'csv',
            'file_path' => 'exports/'.Str::uuid().'.csv',
            'rows_count' => fake()->numberBetween(1, 5000),
            'status' => 'completed',
            'error_message' => null,
            'expires_at' => now()->addDays(7),
            'downloaded_at' => null,
            'ip_address' => fake()->ipv4(),
            'deleted_by' => null,
        ];
    }

    public function processing(): static
    {
        return $this->state(fn () => [
            'status' => 'processing',
            'file_path' => null,
            'rows_count' => 0,
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn () => [
            'status' => 'failed',
            'file_path' => null,
            'rows_count' => 0,
            'error_message' => fake()->sentence(),
        ]);
    }

    public function downloaded(): static
    {
        return $this->state(fn () => ['downloaded_at' => now()]);
    }
}