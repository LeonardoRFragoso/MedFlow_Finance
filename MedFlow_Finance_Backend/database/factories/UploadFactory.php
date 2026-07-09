<?php

namespace Database\Factories;

use App\Models\Upload;
use App\Models\Clinic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UploadFactory extends Factory
{
    protected $model = Upload::class;

    public function definition(): array
    {
        $filename = $this->faker->word() . '.csv';
        $fileHash = hash('sha256', $filename . now()->timestamp);

        return [
            'clinic_id' => Clinic::factory(),
            'user_id' => User::factory(),
            'original_filename' => $filename,
            'file_path' => 'uploads/' . Str::uuid() . '/' . $filename,
            'file_size_bytes' => $this->faker->numberBetween(1000, 1000000),
            'file_type' => 'csv',
            'file_hash' => $fileHash,
            'status' => 'pending',
            'total_rows' => 0,
            'valid_rows' => 0,
            'error_rows' => 0,
            'warning_rows' => 0,
            'billing_period_start' => '2024-01-01',
            'billing_period_end' => '2024-01-31',
            'description' => null,
            'tags' => [],
        ];
    }
}
