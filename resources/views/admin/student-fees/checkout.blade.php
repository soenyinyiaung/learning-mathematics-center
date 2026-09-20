@extends('admin.layout')

@section('content')
<div x-cloak x-data="feeVoucherManagement()" x-init="init()" class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Fee Payment Voucher</h3>
        </div>
        
        <div class="p-6">
            <div x-show="loading" class="text-center py-8 text-gray-500">
                Loading...
            </div>
            
            <div x-show="!loading">
                <div x-show="!invoice" class="text-center py-8 text-gray-500">
                    <i class="fas fa-file-invoice text-4xl mb-3 text-gray-300"></i>
                    <p>No invoice found</p>
                    <a href="{{ route('admin.student-fees') }}" class="inline-block mt-4 text-indigo-600 hover:text-indigo-800">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Student Fees
                    </a>
                </div>
                
                <div x-show="invoice" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Form Section -->
                    <div class="space-y-4">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Payment Information</h4>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Voucher No:</label>
                                <input type="text" x-model="voucherNumber" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date:</label>
                                <input type="date" x-model="voucherDate" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Student Name:</label>
                            <input type="text" x-model="invoice.student.name" class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50" readonly>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Student ID:</label>
                            <input type="text" x-model="invoice.student.student_id" class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50" readonly>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Month/Year:</label>
                            <input type="text" x-model="invoice.month_year" class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50" readonly>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Discount (MMK):</label>
                            <input type="number" x-model="discount" min="0" :max="invoice.amount" step="1000" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="0">
                        </div>
                        
                        <div class="flex gap-3 mt-6">
                            <button @click="savePayment()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-3 px-4 rounded-lg transition-colors">
                                <i class="fas fa-save mr-2"></i>Save & Print
                            </button>
                            <a href="{{ route('admin.student-fees') }}" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white py-3 px-4 rounded-lg transition-colors text-center">
                                <i class="fas fa-arrow-left mr-2"></i>Back to Fees
                            </a>
                        </div>
                    </div>
                    
                    <!-- Voucher Preview Section -->
                    <div class="border-2 border-gray-300 p-4 bg-white" id="fee-voucher-print">
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
                        
                        <!-- Student Info -->
                        <div class="mb-3">
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Student Name:</label>
                                    <div class="w-full px-2 py-1 border border-gray-300 rounded bg-gray-50 text-sm" x-text="invoice.student.name"></div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Student ID:</label>
                                    <div class="w-full px-2 py-1 border border-gray-300 rounded bg-gray-50 text-sm" x-text="invoice.student.student_id"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Fee Details -->
                        <div class="mb-3">
                            <table class="w-full border-collapse border border-gray-300 text-sm">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="border border-gray-300 px-2 py-1 text-left text-xs">Description</th>
                                        <th class="border border-gray-300 px-2 py-1 text-right text-xs">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="subject in studentSubjects" :key="subject.name">
                                        <tr>
                                            <td class="border border-gray-300 px-2 py-1 text-xs" x-text="subject.name"></td>
                                            <td class="border border-gray-300 px-2 py-1 text-right text-xs" x-text="'MMK ' + parseFloat(subject.amount).toFixed(2)"></td>
                                        </tr>
                                    </template>
                                    <template x-if="discount > 0">
                                        <tr>
                                            <td class="border border-gray-300 px-2 py-1 text-xs text-red-600">Discount</td>
                                            <td class="border border-gray-300 px-2 py-1 text-right text-xs text-red-600" x-text="'-MMK ' + parseFloat(discount).toFixed(2)"></td>
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
                                    <div class="w-full px-2 py-1 border border-gray-300 rounded bg-gray-50 font-bold text-right text-base" x-text="'MMK ' + parseFloat(totalAmount).toFixed(2)"></div>
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
    #fee-voucher-print, #fee-voucher-print * {
        visibility: visible;
    }
    #fee-voucher-print {
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