<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\Subject;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Teacher::with('subjects');
        
        if ($request->has('status')) {
            $query->where('status', $request->status === 'active');
        }
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('teacher_id', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        $teachers = $query->paginate(20);
        return response()->json($teachers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'nrc_id' => 'required|string|max:50',
            'employment_type' => 'required|in:full-time,part-time',
            'status' => 'sometimes|boolean',
            'subject_ids' => 'sometimes|array',
            'subject_ids.*' => 'exists:subjects,id'
        ]);

        $validated['status'] = $validated['status'] ?? true;
        $teacher = Teacher::create($validated);
        
        // Attach subjects if provided
        if (isset($validated['subject_ids'])) {
            $teacher->subjects()->attach($validated['subject_ids']);
        }
        
        $teacher->load('subjects');
        return response()->json($teacher, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $teacher = Teacher::with('subjects')->findOrFail($id);
        return response()->json($teacher);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'nrc_id' => 'required|string|max:50',
            'employment_type' => 'required|in:full-time,part-time',
            'status' => 'boolean',
            'subject_ids' => 'sometimes|array',
            'subject_ids.*' => 'exists:subjects,id'
        ]);

        $teacher = Teacher::findOrFail($id);
        $teacher->update($validated);
        
        // Sync subjects if provided
        if (isset($validated['subject_ids'])) {
            $teacher->subjects()->sync($validated['subject_ids']);
        }
        
        $teacher->load('subjects');
        return response()->json($teacher);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $teacher = Teacher::findOrFail($id);
        $teacher->delete();
        return response()->json(null, 204);
    }

    /**
     * Toggle teacher status.
     */
    public function toggleStatus(string $id)
    {
        $teacher = Teacher::findOrFail($id);
        $teacher->status = !$teacher->status;
        $teacher->save();
        return response()->json($teacher);
    }
}
