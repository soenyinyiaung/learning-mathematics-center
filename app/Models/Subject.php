<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subject extends Model
{
    protected $fillable = ['name'];

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_subject');
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Teacher::class, 'teacher_subject');
    }

    public function gradeSubjectFees()
    {
        return $this->hasMany(GradeSubjectFee::class);
    }
}