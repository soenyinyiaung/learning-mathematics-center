@extends('admin.layout')

@section('content')
<div x-cloak x-data="voucherListManagement()" x-init="init()" class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Vouchers</h3>
        </div>
        
        <div class="p-6">
            <div x-show="loading" class="text-center py-8 text-gray-500">
                Loading...
            </div>
            
            <div x-show="!loading">
                <div class="mb-4 flex gap-4">
                    <select x-model="filterType" @change="fetchVouchers()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Types</option>
                        <option value="sale">Sale</option>
                        <option value="student_fee">Student Fee</option>
                    </select>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Voucher Type</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Voucher No</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Date</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Customer</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Customer Type</th>
                                <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">Total</th>
                                <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="voucher in vouchers" :key="voucher.id">
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm">
                                        <span class="px-2 py-1 rounded text-xs" 
                                              :class="voucher.type === 'sale' ? 'bg-purple-100 text-purple-800' : 'bg-orange-100 text-orange-800'"
                                              x-text="voucher.type === 'sale' ? 'Sale' : 'Student Fee'"></span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-800" x-text="voucher.voucher_number"></td>
                                    <td class="px-4 py-3 text-sm text-gray-600" x-text="voucher.voucher_date ? voucher.voucher_date.split('T')[0] : ''"></td>
                                    <td class="px-4 py-3 text-sm text-gray-800" x-text="voucher.customer_name"></td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="px-2 py-1 rounded text-xs" 
                                              :class="voucher.customer_type === 'student' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'"
                                              x-text="voucher.customer_type === 'student' ? 'Student' : 'General'"></span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-800 text-right font-semibold" x-text="'MMK ' + parseFloat(voucher.total_amount).toFixed(2)"></td>
                                    <td class="px-4 py-3 text-center">
                                        <button @click="viewVoucher(voucher)" class="text-indigo-600 hover:text-indigo-800 cursor-pointer">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                
                <div x-show="vouchers.length === 0" class="text-center py-8 text-gray-500">
                    <p>No vouchers found</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- View Voucher Modal -->
    <div x-show="showViewModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" x-cloak>
        <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">Voucher Details</h3>
                <button @click="showViewModal = false" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6" id="voucher-detail">
                <div class="border-2 border-gray-300 p-4 bg-white">
                    <div class="text-center mb-3">
                        <h2 class="text-xl font-bold text-gray-800">LEARNING MATHEMATICS CENTER</h2>
                        <p class="text-sm text-gray-600">Mathematics Education Center</p>
                        <p class="text-xs text-gray-500">Phone: 09-xxx-xxx-xxx | Email: info@lmc.com</p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Voucher Type:</label>
                            <div class="w-full px-2 py-1 border border-gray-300 rounded bg-gray-50 text-sm">
                                <span class="px-2 py-1 rounded text-xs" 
                                      :class="selectedVoucher?.type === 'sale' ? 'bg-purple-100 text-purple-800' : 'bg-orange-100 text-orange-800'"
                                      x-text="selectedVoucher?.type === 'sale' ? 'Sale' : 'Student Fee'"></span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Voucher No:</label>
                            <div class="w-full px-2 py-1 border border-gray-300 rounded bg-gray-50 text-sm" x-text="selectedVoucher?.voucher_number"></div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Date:</label>
                            <div class="w-full px-2 py-1 border border-gray-300 rounded bg-gray-50 text-sm" x-text="selectedVoucher ? selectedVoucher.voucher_date.split('T')[0] : ''"></div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Customer Type:</label>
                            <div class="w-full px-2 py-1 border border-gray-300 rounded bg-gray-50 text-sm">
                                <span class="px-2 py-1 rounded text-xs" 
                                      :class="selectedVoucher?.customer_type === 'student' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'"
                                      x-text="selectedVoucher?.customer_type === 'student' ? 'Student' : 'General'"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Customer Name:</label>
                        <div class="w-full px-2 py-1 border border-gray-300 rounded bg-gray-50 text-sm" x-text="selectedVoucher?.customer_name"></div>
                    </div>
                    
                    <div x-show="selectedVoucher?.student_id_number" class="mb-3">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Student ID:</label>
                        <div class="w-full px-2 py-1 border border-gray-300 rounded bg-gray-50 text-sm" x-text="selectedVoucher?.student_id_number"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Phone:</label>
                        <div class="w-full px-2 py-1 border border-gray-300 rounded bg-gray-50 text-sm" x-text="selectedVoucher?.customer_phone"></div>
                    </div>
                    
                    <div class="mb-3">
                        <!-- Sale Type Table -->
                        <table x-show="selectedVoucher?.type === 'sale'" class="w-full border-collapse border border-gray-300 text-sm">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-300 px-2 py-1 text-left text-xs">No</th>
                                    <th class="border border-gray-300 px-2 py-1 text-left text-xs">Description</th>
                                    <th class="border border-gray-300 px-2 py-1 text-center text-xs">Qty</th>
                                    <th class="border border-gray-300 px-2 py-1 text-right text-xs">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, index) in selectedVoucher?.items" :key="index">
                                    <tr>
                                        <td class="border border-gray-300 px-2 py-1 text-xs" x-text="index + 1"></td>
                                        <td class="border border-gray-300 px-2 py-1 text-xs" x-text="item.name"></td>
                                        <td class="border border-gray-300 px-2 py-1 text-center text-xs" x-text="item.qty"></td>
                                        <td class="border border-gray-300 px-2 py-1 text-right text-xs" x-text="'MMK ' + (parseFloat(item.amount) * item.qty).toFixed(2)"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        
                        <!-- Student Fee Type Table -->
                        <table x-show="selectedVoucher?.type === 'student_fee'" class="w-full border-collapse border border-gray-300 text-sm">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-300 px-2 py-1 text-left text-xs">Description</th>
                                    <th class="border border-gray-300 px-2 py-1 text-right text-xs">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, index) in selectedVoucher?.items" :key="index">
                                    <tr>
                                        <td class="border border-gray-300 px-2 py-1 text-xs" x-text="item.description"></td>
                                        <td class="border border-gray-300 px-2 py-1 text-right text-xs" x-text="'MMK ' + parseFloat(item.amount).toFixed(2)"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mb-3">
                        <div class="flex justify-end">
                            <div class="w-48">
                                <label class="block text-xs font-medium text-gray-700 mb-1">TOTAL:</label>
                                <div class="w-full px-2 py-1 border border-gray-300 rounded bg-gray-50 font-bold text-right text-base" x-text="'MMK ' + parseFloat(selectedVoucher?.total_amount).toFixed(2)"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end">
                <button @click="printVoucher()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
            </div>
        </div>
    </div>
</div>
@endsection