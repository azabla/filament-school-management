<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Classes;
use App\Models\Student;

class Section extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name'
    ];

    public function classes()
    {
        return $this->belongsToMany(Classes::class, 'class_section');
    }
    public function students()
    {
        return $this->hasMany(Student::class, 'section_id');
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'section_teacher');
    }
}