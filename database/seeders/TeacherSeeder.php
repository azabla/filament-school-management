<?php

namespace Database\Seeders;
use App\Models\Teacher; 
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Classes::Teacher();
        Teacher::factory()->count(5)->create();
    }
}