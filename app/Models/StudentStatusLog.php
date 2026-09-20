<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentStatusLog extends Model
{
    protected $fillable = [
        'student_id',
        'status',
        'changed_at'
    ];

    protected $casts = [
        'status' => 'boolean',
        'changed_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
