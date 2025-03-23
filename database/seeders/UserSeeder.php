<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Classes;  
use App\Models\Section;  
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create roles if they don't exist
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $teacherRole = Role::firstOrCreate(['name' => 'Teacher']);
        $studentRole = Role::firstOrCreate(['name' => 'Student']);


          // Get all available class and section IDs
          $classIds = Classes::pluck('id')->toArray(); // Get all class IDs as an array
          $sectionIds = Section::pluck('id')->toArray(); // Get all section IDs as an array

        for ($i = 1; $i <= 30; $i++) {
            $role = ($i % 4 == 0) ? $adminRole :
                    (($i % 5 == 1) ? $teacherRole : $studentRole);
        
            $user = User::create([
                'name' => 'User' . $i,
                'email' => 'user' . $i . '@gmail.com',
                'password' => bcrypt('password123'),
            ]);
        
            $user->assignRole($role);
        
            if ($role->name === 'Student') {
                Student::create([
                    'user_id' => $user->id,
                    'name'=> $user->name,
                    'email' => $user->email,
                    'class_id' => $classIds ? Arr::random($classIds) : null, // Assign a random class ID
                    'section_id' => $sectionIds ? Arr::random($sectionIds) : null, // Assign a random section ID'class_id' => 
                ]);
            }
        
            if ($role->name === 'Teacher') {
                $teacher = Teacher::create([
                    'user_id' => $user->id,
                    'name'=> $user->name,
                    'email' => $user->email,
                ]);

            // Assign random classes (1 to 3 per teacher)
            $teacher->classes()->attach(Arr::random($classIds, rand(1, 3)));

            // Assign random sections (1 to 3 per teacher)
            $teacher->sections()->attach(Arr::random($sectionIds, rand(1, 3)));
            }

            
        }
       
    }
}