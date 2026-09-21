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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($teacher) {
            if (empty($teacher->teacher_id)) {
                $teacher->teacher_id = self::generateTeacherId();
            }
        });
    }

    public static function generateTeacherId()
    {
        $lastTeacher = self::orderBy('id', 'desc')->first();
        
        if ($lastTeacher && $lastTeacher->teacher_id) {
            // Extract the number from the last teacher_id (e.g., TR_01 -> 01)
            $lastNumber = intval(substr($lastTeacher->teacher_id, 3));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Format as TR_01, TR_02, etc.
        return 'TR_' . str_pad($newNumber, 2, '0', STR_PAD_LEFT);
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'teacher_subject');
    }

    public function salaries(): HasMany
    {
        return $this->hasMany(TeacherSalary::class);
    }
}
