<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->lastName(),
            'last_name' => fake()->firstName(),
            'gender' => fake()->randomElement([1, 2, 3]),
            'email' => fake()->email(),
            'tel' => fake()->numerify('090########'),
            'address' => fake()->address(),
            'building' => fake()->optional()->bothify('コーポ?? ###号室'),
            'detail' => fake()->text(120),
        ];
    }
}
