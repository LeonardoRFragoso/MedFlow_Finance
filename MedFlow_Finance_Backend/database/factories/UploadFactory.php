<?php

namespace Database\Factories;

use App\Models\Upload;
use App\Models\Clinic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UploadFactory extends Factory
{
    protected $model = Upload::class;

    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'clinic_id' => Clinic::factory(),
            'user_id' => User::factory(),
            'filename' => $this->faker->word() . '.csv',
            'file_path' => 'uploads/' . $this->faker->uuid() . '/test.csv',
            'file_type' => 'csv',
            'file_size' => $this->faker->numberBetween(1000, 1000000),
            'status' => 'pending',
            'billing_period_start' => '2024-01-01',
            'billing_period_end' => '2024-01-31',
            'total_rows' => 100,
            'processed_rows' => 0,
            'valid_rows' => 0,
            'invalid_rows' => 0,
        ];
    }
}
