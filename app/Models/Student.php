<?php

namespace App\Models;

use App\Models\StudentStatusLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = [
        'student_id',
        'name',
        'phone',
        'birthday',
        'nrc_id',
        'grade_id',
        'guardian_name',
        'guardian_contact',
        'status',
        'registration_status',
        'registration_confirmed_at'
    ];

    protected $casts = [
        'birthday' => 'date',
        'status' => 'boolean',
        'registration_confirmed_at' => 'datetime',
    ];

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'student_subject');
    }

    public function statusLogs()
    {
        return $this->hasMany(StudentStatusLog::class);
    }
}
