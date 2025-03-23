<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\MarkType;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mark>
 */
class MarkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'student_id' => Student::all()->random()->id,  // Using an existing student
            'subject_id' => Subject::all()->random()->id,   // Using an existing subject
            'mark' => $this->faker->numberBetween(50, 100),  // Random mark
            'mark_type_id' => MarkType::all()->random()->id, // Using an existing mark type
        ];
    }
}