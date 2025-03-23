<?php

namespace Database\Seeders;
use App\Models\Section;
use App\Models\Classes;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
            foreach (['Section A', 'Section B', 'Section C', 'Section D', 'Section E'] as $section) {
                Section::create([
                    'name' => $section,
                ]);
            }
    }
}