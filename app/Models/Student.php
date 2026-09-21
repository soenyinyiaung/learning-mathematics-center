<?php

namespace App\Models;

use App\Models\StudentStatusLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Student extends Model
{
    protected $fillable = [
        'student_id',
        'name',
        'phone',
        'birthday',
        'nrc_id',
        'guardian_name',
        'guardian_contact',
        'status'
    ];

    protected $casts = [
        'birthday' => 'date',
        'status' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($student) {
            if (empty($student->student_id)) {
                $lastStudent = static::orderBy('id', 'desc')->first();
                $lastNumber = $lastStudent ? (int) str_replace('LMC_', '', $lastStudent->student_id) : -1;
                $nextNumber = $lastNumber + 1;
                $student->student_id = 'LMC_' . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
            }
        });
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'student_subject');
    }

    public function statusLogs()
    {
        return $this->hasMany(StudentStatusLog::class);
    }

    public function academicYears(): BelongsToMany
    {
        return $this->belongsToMany(AcademicYear::class, 'student_academic_year')
                    ->withPivot('grade_id')
                    ->withTimestamps();
    }

    public function gradeForAcademicYear($academicYearId)
    {
        $academicYear = $this->academicYears()->where('academic_year_id', $academicYearId)->first();
        return $academicYear ? Grade::find($academicYear->pivot->grade_id) : null;
    }
}
