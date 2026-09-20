<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'status'
    ];

    protected $casts = [
        'birthday' => 'date',
        'status' => 'boolean',
    ];

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(StudentStatusLog::class);
    }
}
