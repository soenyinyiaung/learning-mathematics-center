<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subject extends Model
{
    protected $fillable = ['name', 'grade_id'];

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

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