<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AcademicYear extends Model
{
    protected $fillable = [
        'start_year',
        'end_year'
    ];

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_academic_year')
                    ->withPivot('grade_id')
                    ->withTimestamps();
    }

    public function grades(): BelongsToMany
    {
        return $this->belongsToMany(Grade::class, 'student_academic_year')
                    ->distinct();
    }

    public function gradesWithCount()
    {
        return Grade::select('grades.*')
                    ->selectRaw('COUNT(DISTINCT student_academic_year.student_id) as student_count')
                    ->join('student_academic_year', 'grades.id', '=', 'student_academic_year.grade_id')
                    ->where('student_academic_year.academic_year_id', $this->id)
                    ->groupBy('grades.id')
                    ->get();
    }

    public function getYearRangeAttribute(): string
    {
        return "{$this->start_year}-{$this->end_year}";
    }
}
