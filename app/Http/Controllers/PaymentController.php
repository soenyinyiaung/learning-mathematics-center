<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'voucher_no' => 'nullable|string',
            'paid_amount' => 'required|numeric|min:0',
            'paid_date' => 'required|date',
            'discount' => 'nullable|numeric|min:0'
        ]);

        $payment = Payment::create($validated);
        
        // Update invoice status
        $invoice = Invoice::findOrFail($validated['invoice_id']);
        $invoice->status = 'Paid';
        $invoice->save();
        
        $payment->load('invoice.student');
        return response()->json($payment, 201);
    }

    public function index()
    {
        $payments = Payment::with('invoice.student')->orderBy('created_at', 'desc')->paginate(20);
        return response()->json($payments);
    }

    public function show(string $id)
    {
        $payment = Payment::with('invoice.student')->findOrFail($id);
        return response()->json($payment);
    }
}
