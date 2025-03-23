<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = ['Math', 'Science', 'English', 'History', 'Physics'];
    
            foreach ($types as $type) {
                Subject::updateOrCreate(['name' => $type]);
            }

        // Subject::factory()->count(5)->create();
    }
}