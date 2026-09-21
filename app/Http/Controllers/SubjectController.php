<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subjects = Subject::withCount('students')->paginate(40);
        return response()->json($subjects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $subject = Subject::create($validated);
        return response()->json($subject, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $subject = Subject::findOrFail($id);
        return response()->json($subject);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $subject = Subject::findOrFail($id);
        $subject->update($validated);
        return response()->json($subject);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();
        return response()->json(null, 204);
    }

    /**
     * Get students for a specific subject.
     */
    public function getStudents(string $id, Request $request)
    {
        $subject = Subject::findOrFail($id);
        $query = $subject->students();
        
        $students = $query->paginate($request->get('per_page', 10));
        
        // Load grade information for each student based on latest academic year
        $latestAcademicYear = \App\Models\AcademicYear::orderBy('start_year', 'desc')->first();
        if ($latestAcademicYear) {
            foreach ($students as $student) {
                $student->grade = $student->gradeForAcademicYear($latestAcademicYear->id);
            }
        }
        
        return response()->json($students);
    }

    /**
     * Get teachers for a specific subject.
     */
    public function getTeachers(string $id, Request $request)
    {
        $subject = Subject::findOrFail($id);
        $teachers = $subject->teachers()->paginate($request->get('per_page', 10));
        return response()->json($teachers);
    }
}