<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'nts' => strtoupper($this->faker->unique()->bothify('NTSS###########')),
            'address' => $this->faker->streetAddress(),
            'dni' => strtoupper($this->faker->unique()->bothify('########?')),
            'phone' => (int) $this->faker->numerify('#########'),
            'email' => $this->faker->safeEmail(),
            'birth_date' => $this->faker->dateTimeBetween('-90 years', '-10 years'),
        ];
    }
}