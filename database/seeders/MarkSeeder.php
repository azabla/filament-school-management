<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Subject;
use App\Models\MarkType;
use App\Models\Mark;

class MarkSeeder extends Seeder
{
    public function run(): void
    {
        $markTypes = MarkType::all();

        // Retrieve students with their associated subjects from the pivot table
        $students = Student::with('subjects')->get();

        foreach ($students as $student) {
            foreach ($student->subjects as $subject) { // Only subjects linked to the student
                foreach ($markTypes as $markType) {
                    Mark::create([
                        'student_id' => $student->id,
                        'subject_id' => $subject->id,
                        'mark_type_id' => $markType->id,
                        'mark' => rand(0, 100), // Random mark between 0 and 100
                    ]);
                }
            }
        }
    }
}