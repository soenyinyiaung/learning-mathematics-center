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
                    <input type="text" x-model="searchQuery" @input="searchVouchers()" placeholder="Search by voucher number, customer name, or phone..." class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <select x-model="filterType" @change="fetchVouchers()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Types</option>
                        <option value="sale">Sale</option>
                        <option value="student_fee">Student Fee</option>
                        <option value="teacher_salary">Teacher Salary</option>
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
                                              :class="voucher.type === 'sale' ? 'bg-purple-100 text-purple-800' : (voucher.type === 'student_fee' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800')"
                                              x-text="voucher.type === 'sale' ? 'Sale' : (voucher.type === 'student_fee' ? 'Student Fee' : 'Teacher Salary')"></span>
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
        <div class="bg-white rounded-lg shadow-lg max-w-[280px] w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-sm font-semibold text-gray-800">Voucher Details</h3>
                <button @click="showViewModal = false" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-4" id="voucher-detail">
                <div class="border-2 border-gray-300 p-3 bg-white">
                    <div class="text-center mb-2">
                        <h2 class="text-sm font-bold text-gray-800">Tr. Khaing Learning Center</h2>
                        <p class="text-xs text-gray-600">Room No. (8,9,10) Padamyar Street,<br>Forest Quarter, Taunggyi.</p>
                        <p class="text-xs text-gray-500">Ph: 09428311721, 09253947310, 09429911919</p>
                    </div>

                    <div class="grid grid-cols-2 gap-1 mb-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-0.5">Voucher No:</label>
                            <div class="w-full px-1 py-0.5 border border-gray-300 rounded bg-gray-50 text-xs" x-text="selectedVoucher?.voucher_number"></div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-0.5">Date:</label>
                            <div class="w-full px-1 py-0.5 border border-gray-300 rounded bg-gray-50 text-xs" x-text="selectedVoucher ? selectedVoucher.voucher_date.split('T')[0] : ''"></div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="block text-xs font-medium text-gray-700 mb-0.5">Customer Name:</label>
                        <div class="w-full px-1 py-0.5 border border-gray-300 rounded bg-gray-50 text-xs" x-text="selectedVoucher?.customer_name"></div>
                    </div>

                    <div x-show="selectedVoucher?.student_id_number" class="mb-2">
                        <label class="block text-xs font-medium text-gray-700 mb-0.5">Student ID:</label>
                        <div class="w-full px-1 py-0.5 border border-gray-300 rounded bg-gray-50 text-xs" x-text="selectedVoucher?.student_id_number"></div>
                    </div>

                    <div class="mb-2">
                        <label class="block text-xs font-medium text-gray-700 mb-0.5">Payment Method:</label>
                        <div class="w-full px-1 py-0.5 border border-gray-300 rounded bg-gray-50 text-xs">
                            <span class="px-1 py-0.5 rounded text-xs"
                                  :class="selectedVoucher?.payment_method === 'kpay' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'"
                                  x-text="selectedVoucher?.payment_method === 'kpay' ? 'Kpay' : 'Cash'"></span>
                        </div>
                    </div>

                    <div class="mb-2">
                        <!-- Sale Type Table -->
                        <table x-show="selectedVoucher?.type === 'sale'" class="w-full border-collapse border border-gray-300 text-xs">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-300 px-1 py-0.5 text-left text-xs">Item</th>
                                    <th class="border border-gray-300 px-1 py-0.5 text-center text-xs">Qty</th>
                                    <th class="border border-gray-300 px-1 py-0.5 text-right text-xs">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, index) in selectedVoucher?.items" :key="index">
                                    <tr>
                                        <td class="border border-gray-300 px-1 py-0.5 text-xs" x-text="item.name"></td>
                                        <td class="border border-gray-300 px-1 py-0.5 text-center text-xs" x-text="item.qty"></td>
                                        <td class="border border-gray-300 px-1 py-0.5 text-right text-xs" x-text="(parseFloat(item.amount) * item.qty).toFixed(2)"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>

                        <!-- Student Fee Type Table -->
                        <table x-show="selectedVoucher?.type === 'student_fee'" class="w-full border-collapse border border-gray-300 text-xs">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-300 px-1 py-0.5 text-left text-xs">Description</th>
                                    <th class="border border-gray-300 px-1 py-0.5 text-right text-xs">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, index) in selectedVoucher?.items" :key="index">
                                    <tr>
                                        <td class="border border-gray-300 px-1 py-0.5 text-xs" x-text="item.description"></td>
                                        <td class="border border-gray-300 px-1 py-0.5 text-right text-xs" x-text="parseFloat(item.amount).toFixed(2)"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>

                        <!-- Teacher Salary Type Table -->
                        <table x-show="selectedVoucher?.type === 'teacher_salary'" class="w-full border-collapse border border-gray-300 text-xs">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-300 px-1 py-0.5 text-left text-xs">Description</th>
                                    <th class="border border-gray-300 px-1 py-0.5 text-right text-xs">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, index) in selectedVoucher?.items" :key="index">
                                    <tr>
                                        <td class="border border-gray-300 px-1 py-0.5 text-xs" x-text="item.description"></td>
                                        <td class="border border-gray-300 px-1 py-0.5 text-right text-xs" x-text="parseFloat(item.amount).toFixed(2)"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <div class="border-t-2 border-gray-300 pt-2 mb-2">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-gray-800">Total:</span>
                            <span class="text-sm font-bold text-gray-800" x-text="parseFloat(selectedVoucher?.total_amount).toFixed(2) + ' MMK'"></span>
                        </div>
                    </div>

                    <div class="border-t border-dashed border-gray-300 pt-2">
                        <p class="text-xs text-gray-600 text-center">သင်တန်းကြေးများကို လ၏ပထမအပတ်တွင် ပေးသွင်းပေးပါရန်။</p>
                    </div>
                </div>
            </div>
            <div class="p-4 border-t border-gray-200 flex justify-end">
                <button @click="printVoucher()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded text-sm transition-colors">
                    <i class="fas fa-print mr-1"></i>Print
                </button>
            </div>
        </div>
    </div>
</div>
@endsection