<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Mark;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Student extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'class_id',
        'section_id',
        'name',
        'email'
    ];

    public function marks()
    {
        return $this->hasMany(Mark::class, 'student_id');
    }

    // public function subjects()
    // {
    //     return $this->belongsToMany(Subject::class, 'student_subject', 'student_id', 'subject_id');
    // }

    
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'student_subject');
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }
    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
}