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
                    <div class="border-2 border-gray-300 p-4 bg-white" id="voucher-print">
                        <!-- Header -->
                        <div class="text-center mb-3">
                            <h2 class="text-xl font-bold text-gray-800">LEARNING MATHEMATICS CENTER</h2>
                            <p class="text-sm text-gray-600">Mathematics Education Center</p>
                            <p class="text-xs text-gray-500">Phone: 09-xxx-xxx-xxx | Email: info@lmc.com</p>
                        </div>
                        
                        <!-- Voucher Info -->
                        <div class="grid grid-cols-2 gap-2 mb-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Voucher No:</label>
                                <div class="w-full px-2 py-1 border border-gray-300 rounded bg-gray-50 text-sm" x-text="voucherNumber"></div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Date:</label>
                                <div class="w-full px-2 py-1 border border-gray-300 rounded bg-gray-50 text-sm" x-text="voucherDate"></div>
                            </div>
                        </div>
                        
                        <!-- Customer Info -->
                        <div class="mb-3">
                            <label class="block text-xs font-medium text-gray-700 mb-1">
                                <span x-text="customerType === 'student' ? 'Student Name:' : 'Customer Name (Mr/Ms):'"></span>
                            </label>
                            <div class="w-full px-2 py-1 border border-gray-300 rounded bg-gray-50 text-sm" x-text="customerType === 'student' ? (students.find(s => s.id === selectedStudent)?.name || '') : customerName"></div>
                        </div>
                        
                        <div x-show="customerType === 'student'" class="mb-3">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Student ID:</label>
                            <div class="w-full px-2 py-1 border border-gray-300 rounded bg-gray-50 text-sm" x-text="studentId"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Phone:</label>
                            <div class="w-full px-2 py-1 border border-gray-300 rounded bg-gray-50 text-sm" x-text="customerPhone"></div>
                        </div>
                        
                        <!-- Items Table -->
                        <div class="mb-3">
                            <table class="w-full border-collapse border border-gray-300 text-sm">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="border border-gray-300 px-2 py-1 text-left text-xs">No</th>
                                        <th class="border border-gray-300 px-2 py-1 text-left text-xs">Description</th>
                                        <th class="border border-gray-300 px-2 py-1 text-center text-xs">Qty</th>
                                        <th class="border border-gray-300 px-2 py-1 text-right text-xs">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(cartItem, index) in cart" :key="index">
                                        <tr>
                                            <td class="border border-gray-300 px-2 py-1 text-xs" x-text="index + 1"></td>
                                            <td class="border border-gray-300 px-2 py-1 text-xs" x-text="cartItem.name"></td>
                                            <td class="border border-gray-300 px-2 py-1 text-center text-xs" x-text="cartItem.qty"></td>
                                            <td class="border border-gray-300 px-2 py-1 text-right text-xs" x-text="'$' + (parseFloat(cartItem.amount) * cartItem.qty).toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 2})"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Total -->
                        <div class="mb-3">
                            <div class="flex justify-end">
                                <div class="w-48">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">TOTAL:</label>
                                    <div class="w-full px-2 py-1 border border-gray-300 rounded bg-gray-50 font-bold text-right text-base" x-text="'$' + cartTotalPrice.toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 2})"></div>
                                </div>
                            </div>
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
        max-width: 400px;
        margin: 0 auto;
        padding: 20px;
    }
}
</style>
@endsection