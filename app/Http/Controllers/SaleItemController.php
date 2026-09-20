<?php

namespace App\Http\Controllers;

use App\Models\SaleItem;
use Illuminate\Http\Request;

class SaleItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $saleItems = SaleItem::orderBy('created_at', 'desc')->get();
        return response()->json($saleItems);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'qty' => 'required|integer|min:0',
            'amount' => 'required|numeric|min:0'
        ]);

        $saleItem = SaleItem::create($validated);
        return response()->json($saleItem, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $saleItem = SaleItem::findOrFail($id);
        return response()->json($saleItem);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'qty' => 'required|integer|min:0',
            'amount' => 'required|numeric|min:0'
        ]);

        $saleItem = SaleItem::findOrFail($id);
        $saleItem->update($validated);
        return response()->json($saleItem);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $saleItem = SaleItem::findOrFail($id);
        $saleItem->delete();
        return response()->json(null, 204);
    }
}
