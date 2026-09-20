<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Expense::with('category');
        
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        if ($request->has('date_from')) {
            $query->where('expense_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to')) {
            $query->where('expense_date', '<=', $request->date_to);
        }
        
        $expenses = $query->orderBy('expense_date', 'desc')->paginate(20);
        return response()->json($expenses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'category_id' => 'nullable|exists:expense_categories,id',
            'notes' => 'nullable|string'
        ]);

        $expense = Expense::create($validated);
        $expense->load('category');
        return response()->json($expense, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $expense = Expense::with('category')->findOrFail($id);
        return response()->json($expense);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'category_id' => 'nullable|exists:expense_categories,id',
            'notes' => 'nullable|string'
        ]);

        $expense = Expense::findOrFail($id);
        $expense->update($validated);
        $expense->load('category');
        return response()->json($expense);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();
        return response()->json(null, 204);
    }
}
