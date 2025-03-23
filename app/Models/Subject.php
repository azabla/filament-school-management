<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Mark;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subject extends Model
{
    use HasFactory;
    
    protected $fillable = ['name'];
    
    
    public function marks()
    {
        return $this->hasMany(Mark::class);
    }

    public function students():BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_subject');
    }

    public function teachers(){
        return $this->belongsToMany(Teacher::class, 'teacher_subject');
    }
}