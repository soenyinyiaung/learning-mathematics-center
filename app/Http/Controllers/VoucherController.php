<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Models\SaleItem;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $query = Voucher::orderBy('created_at', 'desc');
        
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }
        
        $vouchers = $query->paginate(20);
        return response()->json($vouchers);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'type' => 'required|in:sale,student_fee',
                'voucher_number' => 'required|string|unique:vouchers',
                'voucher_date' => 'required|date',
                'customer_type' => 'nullable|in:general,student',
                'customer_name' => 'nullable|string',
                'customer_phone' => 'nullable|string',
                'student_id' => 'nullable|exists:students,id',
                'student_id_number' => 'nullable|string',
                'total_amount' => 'required|numeric',
                'items' => 'nullable|array'
            ]);

            // Update sale items quantities only for sale type
            if ($validated['type'] === 'sale' && isset($validated['items']) && is_array($validated['items'])) {
                foreach ($validated['items'] as $item) {
                    if (isset($item['id']) && isset($item['qty'])) {
                        $saleItem = SaleItem::find($item['id']);
                        if ($saleItem) {
                            $saleItem->qty = max(0, $saleItem->qty - $item['qty']);
                            $saleItem->save();
                        }
                    }
                }
            }

            $voucher = Voucher::create($validated);
            return response()->json($voucher, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show(string $id)
    {
        $voucher = Voucher::findOrFail($id);
        return response()->json($voucher);
    }
}
