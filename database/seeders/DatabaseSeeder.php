<?php

namespace Database\Seeders;


// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\ClassesSeeder;
use Database\Seeders\SectionSeeder;
use Database\Seeders\TeacherSeeder;
use Database\Seeders\MarkTypeSeeder;
use Database\Seeders\MarkSeeder;
use Database\Seeders\SubjectSeeder;
use Database\Seeders\StudentSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\ClassSectionSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SectionSeeder::class,
            ClassSectionSeeder::class,
            UserSeeder::class,
            // TeacherSeeder::class,
            MarkTypeSeeder::class,
            SubjectSeeder::class,
            // StudentSeeder::class,
            StudentSubjectSeeder::class,
            MarkSeeder::class,
            TeacherSubjectSeeder::class,
        ]);
    }
}