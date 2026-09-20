<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Student;
use App\Models\GradeSubjectFee;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with('student', 'payments')->orderBy('created_at', 'desc');
        
        // Search by student name or ID
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('student', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Filter by student_id
        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        
        $invoices = $query->paginate(20);
        return response()->json($invoices);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'month_year' => 'required|string',
                'amount' => 'sometimes|nullable|numeric|min:0',
                'student_ids' => 'required|array',
                'student_ids.*' => 'exists:students,id'
            ]);

            // Generate invoice for selected students
            $createdInvoices = [];
            
            foreach ($validated['student_ids'] as $studentId) {
                // Calculate amount if not provided
                $amount = $validated['amount'] ?? $this->calculateStudentFee($studentId);
                
                $invoice = Invoice::create([
                    'student_id' => $studentId,
                    'month_year' => $validated['month_year'],
                    'amount' => $amount,
                    'status' => 'Unpaid'
                ]);
                
                $invoice->load('student');
                $createdInvoices[] = $invoice;
            }

            $message = 'Invoices generated successfully';

            return response()->json([
                'message' => $message,
                'invoices' => $createdInvoices
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to generate invoices: ' . $e->getMessage()
            ], 500);
        }
    }

    private function calculateStudentFee($studentId)
    {
        try {
            $student = Student::with('subjects')->findOrFail($studentId);
            $totalFee = 0;
            
            if (!$student->grade_id) {
                \Log::info("Student {$studentId} has no grade assigned");
                return 0;
            }
            
            if ($student->subjects->isEmpty()) {
                \Log::info("Student {$studentId} has no subjects assigned");
                return 0;
            }
            
            \Log::info("Calculating fee for student {$studentId}, grade {$student->grade_id}, subjects: " . $student->subjects->pluck('id')->implode(','));
            
            foreach ($student->subjects as $subject) {
                $gradeSubjectFee = GradeSubjectFee::where('grade_id', $student->grade_id)
                    ->where('subject_id', $subject->id)
                    ->first();
                
                if ($gradeSubjectFee) {
                    $totalFee += $gradeSubjectFee->fee;
                    \Log::info("Found fee for grade {$student->grade_id}, subject {$subject->id}: {$gradeSubjectFee->fee}");
                } else {
                    \Log::info("No fee found for grade {$student->grade_id}, subject {$subject->id}");
                }
            }
            
            \Log::info("Total fee for student {$studentId}: {$totalFee}");
            return $totalFee > 0 ? $totalFee : 0;
        } catch (\Exception $e) {
            \Log::error("Error calculating fee for student {$studentId}: " . $e->getMessage());
            return 0;
        }
    }

    public function show(string $id)
    {
        $invoice = Invoice::with('student', 'payments')->findOrFail($id);
        return response()->json($invoice);
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:Unpaid,Paid',
            'amount' => 'sometimes|numeric|min:0'
        ]);

        $invoice = Invoice::findOrFail($id);
        $invoice->update($validated);
        $invoice->load('student', 'payments');
        return response()->json($invoice);
    }

    public function destroy(string $id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();
        return response()->json(['message' => 'Invoice deleted successfully']);
    }
}
