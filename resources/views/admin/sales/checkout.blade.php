@extends('admin.layout')

@section('content')
<div x-cloak x-data="voucherManagement()" x-init="init()" class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Sales Voucher</h3>
        </div>
        
        <div class="p-6">
            <div x-show="loading" class="text-center py-8 text-gray-500">
                Loading...
            </div>
            
            <div x-show="!loading">
                <div x-show="cart.length === 0" class="text-center py-8 text-gray-500">
                    <i class="fas fa-shopping-cart text-4xl mb-3 text-gray-300"></i>
                    <p>Your cart is empty</p>
                    <a href="{{ route('admin.vouchers') }}" class="inline-block mt-4 text-indigo-600 hover:text-indigo-800">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Vouchers
                    </a>
                </div>
                
                <div x-show="cart.length > 0" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Form Section -->
                    <div class="space-y-4">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Voucher Information</h4>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Voucher No:</label>
                                <input type="text" x-model="voucherNumber" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date:</label>
                                <input type="date" x-model="voucherDate" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Customer Type:</label>
                            <select x-model="customerType" @change="customerTypeChanged()" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="general">General Customer</option>
                                <option value="student">Student</option>
                            </select>
                        </div>
                        
                        <div x-show="customerType === 'general'">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name:</label>
                            <input type="text" x-model="customerName" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter customer name">
                        </div>
                        
                        <div x-show="customerType === 'student'">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Select Student:</label>
                            <select x-model.number="selectedStudent" @change="studentSelected()" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- Select Student --</option>
                                <template x-for="student in students" :key="student.id">
                                    <option :value="student.id" x-text="student.name"></option>
                                </template>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone:</label>
                            <input type="text" x-model="customerPhone" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter phone number">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method:</label>
                            <select x-model="paymentMethod" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="cash">Cash</option>
                                <option value="kpay">Kpay</option>
                            </select>
                        </div>
                        
                        <div class="flex gap-3 mt-6">
                            <button @click="generateVoucher()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-3 px-4 rounded-lg transition-colors">
                                <i class="fas fa-save mr-2"></i>Save Voucher
                            </button>
                            <a href="{{ route('admin.vouchers') }}" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white py-3 px-4 rounded-lg transition-colors text-center">
                                <i class="fas fa-arrow-left mr-2"></i>Back to Vouchers
                            </a>
                        </div>
                    </div>
                    
                    <!-- Voucher Preview Section -->
                    <div class="border-2 border-gray-300 p-3 bg-white" id="voucher-print">
                        <!-- Header -->
                        <div class="text-center mb-2">
                            <h2 class="text-sm font-bold text-gray-800">Tr. Khaing Learning Center</h2>
                            <p class="text-xs text-gray-600">Room No. (8,9,10) Padamyar Street,<br>Forest Quarter, Taunggyi.</p>
                            <p class="text-xs text-gray-500">ph: 09428311721, 09253947310, 09429911919</p>
                        </div>

                        <!-- Voucher Info -->
                        <div class="grid grid-cols-2 gap-1 mb-2">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-0.5">Voucher No:</label>
                                <div class="w-full px-1 py-0.5 border border-gray-300 rounded bg-gray-50 text-xs" x-text="voucherNumber"></div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-0.5">Date:</label>
                                <div class="w-full px-1 py-0.5 border border-gray-300 rounded bg-gray-50 text-xs" x-text="voucherDate"></div>
                            </div>
                        </div>

                        <!-- Customer Info -->
                        <div class="mb-2">
                            <label class="block text-xs font-medium text-gray-700 mb-0.5">
                                <span x-text="customerType === 'student' ? 'Student Name:' : 'Customer Name (Mr/Ms):'"></span>
                            </label>
                            <div class="w-full px-1 py-0.5 border border-gray-300 rounded bg-gray-50 text-xs" x-text="customerType === 'student' ? (students.find(s => s.id === selectedStudent)?.name || '') : customerName"></div>
                        </div>

                        <div x-show="customerType === 'student'" class="mb-2">
                            <label class="block text-xs font-medium text-gray-700 mb-0.5">Student ID:</label>
                            <div class="w-full px-1 py-0.5 border border-gray-300 rounded bg-gray-50 text-xs" x-text="studentId"></div>
                        </div>

                        <div class="mb-2">
                            <label class="block text-xs font-medium text-gray-700 mb-0.5">Phone:</label>
                            <div class="w-full px-1 py-0.5 border border-gray-300 rounded bg-gray-50 text-xs" x-text="customerPhone"></div>
                        </div>

                        <!-- Items Table -->
                        <div class="mb-2">
                            <table class="w-full border-collapse border border-gray-300 text-xs">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="border border-gray-300 px-1 py-0.5 text-left text-xs">Item</th>
                                        <th class="border border-gray-300 px-1 py-0.5 text-center text-xs">Qty</th>
                                        <th class="border border-gray-300 px-1 py-0.5 text-right text-xs">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(cartItem, index) in cart" :key="index">
                                        <tr>
                                            <td class="border border-gray-300 px-1 py-0.5 text-xs" x-text="cartItem.name"></td>
                                            <td class="border border-gray-300 px-1 py-0.5 text-center text-xs" x-text="cartItem.qty"></td>
                                            <td class="border border-gray-300 px-1 py-0.5 text-right text-xs" x-text="(parseFloat(cartItem.amount) * cartItem.qty).toFixed(2)"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <!-- Total -->
                        <div class="border-t-2 border-gray-300 pt-2 mb-2">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-gray-800">Total:</span>
                                <span class="text-sm font-bold text-gray-800" x-text="cartTotalPrice.toFixed(2) + ' MMK'"></span>
                            </div>
                        </div>

                        <div class="border-t border-dashed border-gray-300 pt-2">
                            <p class="text-xs text-gray-600 text-center">သင်တန်းကြေးများကို လ၏ပထမအပတ်တွင် ပေးသွင်းပေးပါရန်။</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #voucher-print, #voucher-print * {
        visibility: visible;
    }
    #voucher-print {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        max-width: 280px;
        margin: 0 auto;
        padding: 10px;
    }
}
</style>
@endsection