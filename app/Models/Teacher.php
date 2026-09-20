<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    protected $fillable = [
        'teacher_id',
        'name',
        'phone',
        'nrc_id',
        'employment_type',
        'subject_id',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function salaries(): HasMany
    {
        return $this->hasMany(TeacherSalary::class);
    }
}
