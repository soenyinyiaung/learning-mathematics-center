<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\Student;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::orderBy('start_year', 'desc')->get();
        return response()->json($academicYears);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'start_year' => 'required|integer|min:2000|max:2100',
            'end_year' => 'required|integer|min:2000|max:2100|gt:start_year'
        ]);

        $academicYear = AcademicYear::create($validated);
        return response()->json($academicYear, 201);
    }

    public function show($id)
    {
        $academicYear = AcademicYear::findOrFail($id);
        return response()->json($academicYear);
    }

    public function getStudents($academicYearId, $gradeId)
    {
        $students = Student::whereHas('academicYears', function($query) use ($academicYearId, $gradeId) {
            $query->where('academic_year_id', $academicYearId)
                  ->where('grade_id', $gradeId);
        })->get();

        // Load grade information for each student
        $grade = Grade::find($gradeId);
        foreach ($students as $student) {
            $student->grade = $grade;
        }

        return response()->json($students);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'start_year' => 'sometimes|integer|min:2000|max:2100',
            'end_year' => 'sometimes|integer|min:2000|max:2100|gt:start_year'
        ]);

        $academicYear = AcademicYear::findOrFail($id);
        $academicYear->update($validated);
        return response()->json($academicYear);
    }

    public function destroy($id)
    {
        $academicYear = AcademicYear::findOrFail($id);
        $academicYear->delete();
        return response()->json(null, 204);
    }

    public function addStudent(Request $request, $academicYearId)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'grade_id' => 'required|exists:grades,id'
        ]);

        $academicYear = AcademicYear::findOrFail($academicYearId);
        $addedCount = 0;

        foreach ($validated['student_ids'] as $studentId) {
            $student = Student::findOrFail($studentId);

            // Check if student is already in this academic year
            if (!$student->academicYears()->where('academic_year_id', $academicYearId)->exists()) {
                $student->academicYears()->attach($academicYearId, [
                    'grade_id' => $validated['grade_id']
                ]);
                $addedCount++;
            }
        }

        return response()->json(['message' => "Added {$addedCount} students to academic year successfully"], 201);
    }

    public function removeStudent($academicYearId, $studentId)
    {
        $academicYear = AcademicYear::findOrFail($academicYearId);
        $student = Student::findOrFail($studentId);

        $student->academicYears()->detach($academicYearId);

        return response()->json(['message' => 'Student removed from academic year successfully']);
    }
}
