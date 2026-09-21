<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\Student;
use App\Models\StudentRegistration;
use Illuminate\Http\Request;

class StudentRegistrationController extends Controller
{
    public function index(Request $request)
    {
        $query = StudentRegistration::with('grade', 'subjects');
        
        // Search by student name
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }
        
        $registrations = $query->orderBy('created_at', 'desc')->paginate(20);
        return response()->json($registrations);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'birthday' => 'required|date',
            'nrc_id' => 'required|string|max:50',
            'grade_id' => 'required|exists:grades,id',
            'guardian_name' => 'required|string|max:255',
            'guardian_contact' => 'required|string|max:20',
            'subject_ids' => 'sometimes|array',
            'subject_ids.*' => 'exists:subjects,id'
        ]);

        $validated['status'] = false;
        
        // Store grade_id temporarily for response
        $gradeId = $validated['grade_id'];
        unset($validated['grade_id']);
        
        $registration = StudentRegistration::create($validated);
        
        // Attach subjects if provided
        if (isset($validated['subject_ids'])) {
            $registration->subjects()->attach($validated['subject_ids']);
        }
        
        // Add grade info for response
        $registration->grade_id = $gradeId;
        $registration->grade = Grade::find($gradeId);
        
        $registration->load('subjects');
        return response()->json($registration, 201);
    }
    
    public function approve(Request $request, $id)
    {
        $registration = StudentRegistration::with('grade', 'subjects')->findOrFail($id);
        
        // Move to students table
        $student = Student::create([
            'name' => $registration->name,
            'phone' => $registration->phone,
            'birthday' => $registration->birthday,
            'nrc_id' => $registration->nrc_id,
            'guardian_name' => $registration->guardian_name,
            'guardian_contact' => $registration->guardian_contact,
            'status' => true
        ]);
        
        // Attach subjects
        if ($registration->subjects) {
            $student->subjects()->attach($registration->subjects->pluck('id'));
        }

        // Add to latest academic year with the grade
        $latestAcademicYear = AcademicYear::orderBy('start_year', 'desc')->first();
        if ($latestAcademicYear && $registration->grade_id) {
            $student->academicYears()->attach($latestAcademicYear->id, [
                'grade_id' => $registration->grade_id
            ]);
        }
        
        // Delete the registration
        $registration->delete();
        
        $student->load('subjects');
        $student->grade = $student->gradeForAcademicYear($latestAcademicYear->id);
        return response()->json($student);
    }
    
    public function reject(Request $request, $id)
    {
        $registration = StudentRegistration::findOrFail($id);
        $registration->delete();
        return response()->json(['message' => 'Registration rejected and deleted']);
    }
    
    public function show($id)
    {
        $registration = StudentRegistration::with('grade', 'subjects')->findOrFail($id);
        return response()->json($registration);
    }
}
