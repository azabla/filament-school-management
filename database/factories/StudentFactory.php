<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Classes;
use App\Models\Section;
use App\Models\User;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::all()->random()->id,
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'class_id' => Classes::all()->random()->id,  // Using an existing Class
            'section_id' => Section::all()->random()->id,  // Using an existing Section
        ];
    }
}