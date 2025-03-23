<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Student;
use App\Models\Teacher;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;
   
        protected function handleRecordUpdate(Model $user, array $data): Model
        {
        // Create the user
        $user->update($data);
        
         $id = $data['roles'] ?? [];
         $roles = \Spatie\Permission\Models\Role::whereIn('id', $id)->pluck('name')->toArray();
         

        if (in_array('Student', $roles)) {
            // Update if the student exists, otherwise create a new record
            Student::updateOrCreate(
                ['user_id' => $user->id], // Condition to find an existing record
                [
                    'class_id' => $data['class_id'] ?? null,
                    'section_id' => $data['section_id'] ?? null,
                    'name' => $user->name,
                    'email' => $user->email,
                ]
            );
        } elseif (in_array('Teacher', $roles)) {
            // Update if the teacher exists, otherwise create a new record
            Teacher::updateOrCreate(
                ['user_id' => $user->id], // Condition to find an existing record
                [
                    'name' => $user->name,
                    'email' => $user->email,
                ]
            );
        }
        
        return $user;
    }


    protected function mutateFormDataBeforeFill(array $data): array
    {
        $user = $this->getRecord(); 
        
        $roleNames = $user->roles->pluck('name')->toArray();
        $data['roles'] = $user->roles->pluck('id')->toArray(); 
        \Log::info('Roles:', $roleNames);

        // $student = $user->student; 
        // \Log::info('student:', $student);
        
        if(in_array('Student', $roleNames)) {
            $student = Student::where('user_id', $user->id)->first(); 

            $data['class_id'] = $student?->class_id ?? null;
            $data['section_id'] = $student?->section_id ?? null; 

        } 
        
        //Set visibility flags explicitly
        $data['is_student'] = in_array('Student', $roleNames);
        $data['is_teacher'] = in_array('Teacher', $roleNames);
    
        return $data;
    }
    
}