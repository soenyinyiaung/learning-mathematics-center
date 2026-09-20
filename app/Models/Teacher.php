<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    protected $fillable = [
        'teacher_id',
        'name',
        'phone',
        'nrc_id',
        'employment_type',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'teacher_subject');
    }

    public function salaries(): HasMany
    {
        return $this->hasMany(TeacherSalary::class);
    }
}
