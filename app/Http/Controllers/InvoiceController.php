<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
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
        $validated = $request->validate([
            'month_year' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id'
        ]);

        // Generate invoice for selected students
        $createdInvoices = [];
        foreach ($validated['student_ids'] as $studentId) {
            // Check if invoice already exists for this month
            $existingInvoice = Invoice::where('student_id', $studentId)
                ->where('month_year', $validated['month_year'])
                ->first();
            
            if (!$existingInvoice) {
                $invoice = Invoice::create([
                    'student_id' => $studentId,
                    'month_year' => $validated['month_year'],
                    'amount' => $validated['amount'],
                    'status' => 'Unpaid'
                ]);
                
                $invoice->load('student');
                $createdInvoices[] = $invoice;
            }
        }

        return response()->json([
            'message' => 'Invoices generated successfully',
            'invoices' => $createdInvoices
        ], 201);
    }

    public function show(string $id)
    {
        $invoice = Invoice::with('student', 'payments')->findOrFail($id);
        return response()->json($invoice);
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:Unpaid,Paid'
        ]);

        $invoice = Invoice::findOrFail($id);
        $invoice->update($validated);
        $invoice->load('student', 'payments');
        return response()->json($invoice);
    }
}
