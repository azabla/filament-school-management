<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Classes;
use App\Models\Section;

class ClassSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 classes
        Classes::factory()->count(10)->create()->each(function ($class) {
            // Get all existing sections
            $sections = Section::all();
            
            // Randomly attach 2-5 sections from the existing ones to each class
            $class->sections()->attach(
                $sections->random(rand(2, 5))->pluck('id')->toArray()
            );
        });
    }
}