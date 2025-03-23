<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Teacher;
use App\Models\Subject;

class TeacherSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          // Get all teachers and subjects
          $teachers = Teacher::all();
          $subjects = Subject::all();
  
          // Loop through teachers and assign them random subjects
          foreach ($teachers as $teacher) {
              $randomSubjects = $subjects->random(rand(1, 3)); // Randomly assign 1-3 subjects
              $teacher->subjects()->attach($randomSubjects); // Attach subjects to teacher
          }
    }
}