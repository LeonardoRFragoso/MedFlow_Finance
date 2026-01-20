<?php

namespace Database\Factories;

use App\Models\Record;
use App\Models\Clinic;
use App\Models\Upload;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecordFactory extends Factory
{
    protected $model = Record::class;

    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'clinic_id' => Clinic::factory(),
            'upload_id' => Upload::factory(),
            'status' => 'pending',
            'patient_name' => $this->faker->name(),
            'patient_cpf' => $this->faker->numerify('###########'),
            'health_plan' => $this->faker->company(),
            'procedure_code' => $this->faker->numerify('######'),
            'procedure_name' => $this->faker->words(3, true),
            'procedure_date' => '2024-01-15',
            'amount_billed' => 1000.00,
            'authorization_code' => $this->faker->bothify('??###'),
            'error_count' => 0,
        ];
    }
}
