<?php

namespace mmerlijn\patient\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use mmerlijn\patient\Models\Patient;

class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition()
    {
        $this->faker = \Faker\Factory::create('nl_NL');
        return [
            'sex' => $this->faker->randomElement(['M', 'F']),
            'own_lastname' => $this->faker->lastName,
            'lastname' => $this->faker->optional(0.5)->lastName,
            'initials' => $this->faker->randomLetter,
            'dob' => $this->faker->dateTimeThisCentury->format('Y-m-d'),
            'bsn' => $this->faker->idNumber,
            'postcode' => $this->faker->postcode,
            'building_nr' => $this->faker->buildingNumber,
            'street' => $this->faker->streetName,
            'city' => $this->faker->city,
            'phone' => $this->faker->phoneNumber,
        ];
    }
}