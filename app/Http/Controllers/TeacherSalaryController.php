<?php

namespace App\Http\Controllers;

use App\Models\TeacherSalary;
use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherSalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TeacherSalary::with('teacher');
        
        if ($request->has('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }
        
        if ($request->has('date_from')) {
            $query->where('salary_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to')) {
            $query->where('salary_date', '<=', $request->date_to);
        }
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('teacher', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }
        
        $salaries = $query->orderBy('salary_date', 'desc')->paginate(20);
        return response()->json($salaries);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'amount' => 'required|numeric|min:0',
            'salary_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        $salary = TeacherSalary::create($validated);
        $salary->load('teacher');
        return response()->json($salary, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $salary = TeacherSalary::with('teacher')->findOrFail($id);
        return response()->json($salary);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'amount' => 'required|numeric|min:0',
            'salary_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        $salary = TeacherSalary::findOrFail($id);
        $salary->update($validated);
        $salary->load('teacher');
        return response()->json($salary);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $salary = TeacherSalary::findOrFail($id);
        $salary->delete();
        return response()->json(null, 204);
    }
}
