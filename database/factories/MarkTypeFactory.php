<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MarkType>
 */
class MarkTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mark_type' => $this->faker->randomElement(['Quiz', 'Final Exam', 'Test']),
            'amount' => $this->faker->numberBetween(5, 100),
        ];
    }
}