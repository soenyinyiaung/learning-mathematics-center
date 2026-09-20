<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherSalary extends Model
{
    protected $fillable = [
        'teacher_id',
        'amount',
        'salary_date',
        'notes'
    ];

    protected $casts = [
        'salary_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }
}
