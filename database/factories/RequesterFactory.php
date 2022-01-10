<?php

namespace mmerlijn\patient\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use mmerlijn\patient\Models\Requester;

class RequesterFactory extends Factory
{
    protected $model = Requester::class;

    public function definition()
    {
        $this->faker = \Faker\Factory::create('nl_NL');
        return [
            'sex' => $this->faker->randomElement(['M', 'F']),
            'lastname' => $this->faker->lastName,
            'initials' => $this->faker->randomLetter,
            'postcode' => $this->faker->postcode,
            'building' => $this->faker->buildingNumber,
            'street' => $this->faker->streetName,
            'city' => $this->faker->city,
            'phone' => $this->faker->phoneNumber,
            'agbcode' => $this->faker->numberBetween(100000000, 999999999),
        ];
    }

    public function relations(array $relations)
    {
        return $this->state(fn($attributes) => ['relations' => $relations]);
    }
}