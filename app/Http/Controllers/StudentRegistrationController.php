<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentRegistrationController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with('grade', 'subjects');
        
        // Filter by registration status
        if ($request->has('status') && $request->status) {
            $query->where('registration_status', $request->status);
        }
        
        // Search by student name or ID
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }
        
        $students = $query->orderBy('created_at', 'desc')->paginate(20);
        return response()->json($students);
    }
    
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'registration_status' => 'required|in:confirmed,rejected'
        ]);
        
        $student = Student::findOrFail($id);
        $student->registration_status = $validated['registration_status'];
        
        if ($validated['registration_status'] === 'confirmed') {
            $student->registration_confirmed_at = now();
            $student->status = true; // Activate student when confirmed
        } else {
            $student->registration_confirmed_at = null;
            $student->status = false; // Deactivate student when rejected
        }
        
        $student->save();
        $student->load('grade', 'subjects');
        
        return response()->json($student);
    }
    
    public function show($id)
    {
        $student = Student::with('grade', 'subjects')->findOrFail($id);
        return response()->json($student);
    }
}
