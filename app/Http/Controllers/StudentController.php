<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentStatusLog;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Student::with('grade');
        
        if ($request->has('status')) {
            $query->where('status', $request->status === 'active');
        }
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        $students = $query->paginate(20);
        return response()->json($students);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|string|unique:students,student_id',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'birthday' => 'required|date',
            'nrc_id' => 'required|string|max:50',
            'grade_id' => 'required|exists:grades,id',
            'guardian_name' => 'required|string|max:255',
            'guardian_contact' => 'required|string|max:20',
            'status' => 'sometimes|boolean'
        ]);

        $validated['status'] = $validated['status'] ?? true;
        $student = Student::create($validated);
        $student->load('grade');
        return response()->json($student, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $student = Student::with('grade')->findOrFail($id);
        return response()->json($student);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'student_id' => 'required|string|unique:students,student_id,' . $id,
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'birthday' => 'required|date',
            'nrc_id' => 'required|string|max:50',
            'grade_id' => 'required|exists:grades,id',
            'guardian_name' => 'required|string|max:255',
            'guardian_contact' => 'required|string|max:20',
            'status' => 'boolean'
        ]);

        $student = Student::findOrFail($id);
        $student->update($validated);
        $student->load('grade');
        return response()->json($student);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $student = Student::findOrFail($id);
        $student->delete();
        return response()->json(null, 204);
    }

    /**
     * Toggle student status.
     */
    public function toggleStatus(string $id)
    {
        $student = Student::findOrFail($id);
        $student->status = !$student->status;
        $student->save();
        
        // Create status log
        StudentStatusLog::create([
            'student_id' => $student->id,
            'status' => $student->status,
            'changed_at' => now()
        ]);
        
        $student->load('grade');
        return response()->json($student);
    }
}