@extends('admin.layout')

@section('content')
<div x-cloak x-data="studentFeeManagement()" x-init="init()" class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Student Fees</h3>
            <button @click="showGenerateModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-file-invoice mr-2"></i>Generate Invoices
            </button>
        </div>
        
        <div class="p-6">
            <!-- Search Section -->
            <div class="mb-6 flex gap-4">
                <input type="text" x-model="searchQuery" @input="searchStudents()" placeholder="Search by student name or ID..." class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <select x-model="filterStatus" @change="searchStudents()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Status</option>
                    <option value="Unpaid">Unpaid</option>
                    <option value="Paid">Paid</option>
                </select>
            </div>
            
            <!-- Unpaid Invoices List -->
            <div x-show="loading" class="text-center py-8 text-gray-500">
                Loading...
            </div>
            
            <div x-show="!loading">
                <div x-show="invoices.length === 0" class="text-center py-8 text-gray-500">
                    <p>No invoices found</p>
                </div>
                
                <div x-show="invoices.length > 0" class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Student</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Month/Year</th>
                                <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">Amount</th>
                                <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Status</th>
                                <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="invoice in invoices" :key="invoice.id">
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-800" x-text="invoice.student?.name"></td>
                                    <td class="px-4 py-3 text-sm text-gray-600" x-text="invoice.month_year"></td>
                                    <td class="px-4 py-3 text-sm text-gray-800 text-right font-semibold" x-text="'$' + parseFloat(invoice.amount).toFixed(2)"></td>
                                    <td class="px-4 py-3 text-center text-sm">
                                        <span class="px-2 py-1 rounded text-xs" 
                                              :class="invoice.status === 'Paid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                              x-text="invoice.status"></span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <template x-if="invoice.status === 'Unpaid'">
                                            <button @click="payInvoice(invoice)" class="text-green-600 hover:text-green-800 cursor-pointer">
                                                <i class="fas fa-money-bill-wave"></i>
                                            </button>
                                        </template>
                                        <button @click="viewHistory(invoice.student)" class="text-indigo-600 hover:text-indigo-800 cursor-pointer ml-2">
                                            <i class="fas fa-history"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Generate Invoices Modal -->
    <div x-show="showGenerateModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" x-cloak>
        <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Generate Invoices</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Month/Year</label>
                    <input type="month" x-model="selectedMonthYear" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount (MMK)</label>
                    <input type="number" x-model="selectedAmount" min="0" step="1000" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., 100000">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Students</label>
                    <div class="mb-2">
                        <label class="inline-flex items-center">
                            <input type="checkbox" x-model="selectAllStudents" @change="toggleSelectAllStudents()" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">Select All Active Students</span>
                        </label>
                    </div>
                    <div class="border border-gray-300 rounded-lg max-h-48 overflow-y-auto p-2">
                        <template x-for="student in students" :key="student.id">
                            <label class="flex items-center py-1 hover:bg-gray-50 px-2 rounded">
                                <input type="checkbox" :value="student.id" x-model="selectedStudentIds" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700" x-text="student.name + ' (' + (student.student_id || 'N/A') + ')'"></span>
                            </label>
                        </template>
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                <button @click="showGenerateModal = false" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</button>
                <button @click="generateInvoices()" class="px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white">Generate</button>
            </div>
        </div>
    </div>
    
    <!-- Student History Modal -->
    <div x-show="showHistoryModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" x-cloak>
        <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">Payment History</h3>
                <button @click="showHistoryModal = false" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <h4 class="font-semibold text-gray-800" x-text="selectedStudent?.name"></h4>
                    <p class="text-sm text-gray-600" x-text="'Student ID: ' + (selectedStudent?.student_id || 'N/A')"></p>
                </div>
                
                <div class="space-y-4">
                    <template x-for="invoice in studentInvoices" :key="invoice.id">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h5 class="font-medium text-gray-800" x-text="invoice.month_year"></h5>
                                    <p class="text-sm text-gray-600">Amount: <span class="font-semibold" x-text="'$' + parseFloat(invoice.amount).toFixed(2)"></span></p>
                                </div>
                                <span class="px-2 py-1 rounded text-xs" 
                                      :class="invoice.status === 'Paid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                      x-text="invoice.status"></span>
                            </div>
                            <template x-if="invoice.payments && invoice.payments.length > 0">
                                <div class="mt-2 pt-2 border-t border-gray-200">
                                    <p class="text-xs text-gray-500 mb-1">Payment Details:</p>
                                    <template x-for="payment in invoice.payments" :key="payment.id">
                                        <div class="text-sm text-gray-600">
                                            <span x-text="'Paid: $' + parseFloat(payment.paid_amount).toFixed(2)"></span>
                                            <span class="ml-2" x-text="'(' + payment.paid_date + ')'"></span>
                                            <span x-if="payment.voucher_no" class="ml-2 text-xs text-gray-500" x-text="'Voucher: ' + payment.voucher_no"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection