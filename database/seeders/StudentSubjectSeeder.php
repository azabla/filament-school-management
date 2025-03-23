<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Subject;

class StudentSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Get all students and subjects
         $students = Student::all();
         $subjects = Subject::all();
 
         // Loop through students and assign them random subjects
         foreach ($students as $student) {
             $randomSubjects = $subjects->random(rand(1, 3)); // Randomly assign 1-3 subjects
             $student->subjects()->attach($randomSubjects); // Attach subjects to student
         }
    }
}