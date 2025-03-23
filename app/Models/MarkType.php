<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Mark;

class MarkType extends Model
{
    use HasFactory;
    protected $fillable = ['mark_type', 'amount'];

    public function marks()
    {
        return $this->hasMany(Mark::class);
    }
}