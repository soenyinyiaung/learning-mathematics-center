<?php

namespace App\Http\Controllers;

use App\Models\GradeSubjectFee;
use Illuminate\Http\Request;

class GradeSubjectFeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = GradeSubjectFee::with(['grade', 'subject']);
        
        // Filter by grade
        if ($request->has('grade_id') && $request->grade_id) {
            $query->where('grade_id', $request->grade_id);
        }
        
        // Filter by subject
        if ($request->has('subject_id') && $request->subject_id) {
            $query->where('subject_id', $request->subject_id);
        }
        
        // Sort by grade name if both filters are empty
        if ($request->has('sort') && $request->sort === 'grade_name') {
            $query->join('grades', 'grade_subject_fees.grade_id', '=', 'grades.id')
                  ->select('grade_subject_fees.*')
                  ->orderBy('grades.name', 'asc');
        }
        
        $fees = $query->paginate(20);
        return response()->json($fees);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'grade_id' => 'required|exists:grades,id',
            'subject_id' => 'required|exists:subjects,id',
            'fee' => 'required|numeric|min:0'
        ]);

        $fee = GradeSubjectFee::updateOrCreate(
            [
                'grade_id' => $validated['grade_id'],
                'subject_id' => $validated['subject_id']
            ],
            [
                'fee' => $validated['fee']
            ]
        );

        return response()->json($fee->load(['grade', 'subject']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $fee = GradeSubjectFee::with(['grade', 'subject'])->findOrFail($id);
        return response()->json($fee);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'fee' => 'required|numeric|min:0'
        ]);

        $fee = GradeSubjectFee::findOrFail($id);
        $fee->update($validated);

        return response()->json($fee->load(['grade', 'subject']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $fee = GradeSubjectFee::findOrFail($id);
        $fee->delete();
        return response()->json(null, 204);
    }

    /**
     * Get fees by grade.
     */
    public function getByGrade($gradeId)
    {
        $fees = GradeSubjectFee::with('subject')
            ->where('grade_id', $gradeId)
            ->get();
        return response()->json($fees);
    }

    /**
     * Get fees by subject.
     */
    public function getBySubject($subjectId)
    {
        $fees = GradeSubjectFee::with('grade')
            ->where('subject_id', $subjectId)
            ->get();
        return response()->json($fees);
    }
}
