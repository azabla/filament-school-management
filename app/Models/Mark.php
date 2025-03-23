<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Student;
use App\Models\Subject;
use App\Models\MarkType;
use App\Models\Teacher;

class Mark extends Model
{
    use HasFactory;
    protected $fillable = ['student_id', 'subject_id', 'mark_type_id','mark'];

    public function student()  
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function markType()
    {
        return $this->belongsTo(MarkType::class, 'mark_type_id');
    }
  
}