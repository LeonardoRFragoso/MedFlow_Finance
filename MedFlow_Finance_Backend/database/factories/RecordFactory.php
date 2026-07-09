<?php

namespace Database\Factories;

use App\Models\Record;
use App\Models\Clinic;
use App\Models\Upload;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RecordFactory extends Factory
{
    protected $model = Record::class;

    public function definition(): array
    {
        return [
            'clinic_id' => Clinic::factory(),
            'upload_id' => Upload::factory(),
            'patient_name' => $this->faker->name(),
            'patient_cpf' => $this->faker->numerify('###########'),
            'patient_id' => Str::uuid(),
            'procedure_code' => $this->faker->numerify('PROC###'),
            'procedure_name' => $this->faker->words(3, true),
            'procedure_date' => '2024-01-15',
            'amount_billed' => $this->faker->randomFloat(2, 100, 5000),
            'amount_paid' => $this->faker->randomFloat(2, 0, 5000),
            'amount_pending' => $this->faker->randomFloat(2, 0, 5000),
            'status' => 'pending',
            'provider_name' => $this->faker->company(),
            'provider_id' => Str::uuid(),
            'insurance_name' => $this->faker->company(),
            'insurance_id' => $this->faker->bothify('INS###'),
            'authorization_number' => $this->faker->bothify('AUTH###'),
            'raw_data' => [
                'source' => 'factory',
                'generated_for' => 'test',
            ],
        ];
    }
}
