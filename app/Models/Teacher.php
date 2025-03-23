<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Subject;
use App\Models\Mark;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'email'];

    // A teacher assigns marks to students
    public function marks()
    {
        return $this->hasMany(Mark::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'teacher_subject');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function classes()
    {
        return $this->belongsToMany(Classes::class, 'class_teacher');
    }
    public function sections()
    {
        return $this->belongsToMany(Section::class, 'section_teacher');
    }
}