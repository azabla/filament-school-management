<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Student;
use App\Models\Teacher;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = static::getModel()::create($data);
        
        $id = $data['roles'] ?? [];
        $roles = \Spatie\Permission\Models\Role::whereIn('id', $id)->pluck('name')->toArray();
        \Log::info('roles', $roles);
        \Log::info('data', $data);
        if (in_array('Student', $roles)) {
        \Log::info('in isde ');
            
            Student::create([
                'user_id' => $user->id,
                'class_id' => $data['class_id'] ?? null,
                'section_id' => $data['section_id'] ?? null,
                'name' => $user->name,
                'email' => $user->email,
            ]);
        } elseif (in_array('Teacher', $roles)) {
            Teacher::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]);
        }
      
        return $user;
    }
}