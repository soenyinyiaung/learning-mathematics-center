@extends('admin.layout')

@section('content')
<div x-cloak x-data="teacherSalaryManagement()" x-init="init()" class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Teacher Salaries</h3>
            <button @click="openAddModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>Add New Salary
            </button>
        </div>
        
        <div class="p-6">
            <!-- Search -->
            <div class="mb-4">
                <input type="text" x-model="searchQuery" @input="searchSalaries()" placeholder="Search by teacher name..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div x-show="loading" class="text-center py-8 text-gray-500">
                Loading...
            </div>
            
            <div x-show="!loading" class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">ID</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Teacher</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Amount</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Date</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Notes</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="salary in salaries" :key="salary.id">
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-3 px-4 text-gray-600" x-text="salary.id"></td>
                                <td class="py-3 px-4 text-gray-800 font-medium" x-text="getTeacherName(salary.teacher_id)"></td>
                                <td class="py-3 px-4 text-gray-800 font-semibold" x-text="formatAmount(salary.amount)"></td>
                                <td class="py-3 px-4 text-gray-600" x-text="formatDate(salary.salary_date)"></td>
                                <td class="py-3 px-4 text-gray-600 text-sm max-w-xs truncate" x-text="salary.notes || '-'"></td>
                                <td class="py-3 px-4 text-right">
                                    <button @click="generateVoucher(salary)" class="text-green-600 hover:text-green-800 mr-3 cursor-pointer" title="Generate Voucher">
                                        <i class="fas fa-file-invoice"></i>
                                    </button>
                                    <button @click="editSalary(salary)" class="text-indigo-600 hover:text-indigo-800 mr-3 cursor-pointer">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button @click="deleteSalary(salary.id)" class="text-red-600 hover:text-red-800 cursor-pointer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="salaries.length === 0">
                            <td colspan="6" class="py-8 text-center text-gray-500">No salaries found</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div x-show="!loading && salaries.length > 0" class="mt-6 flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    Page <span x-text="currentPage"></span> of <span x-text="lastPage"></span>
                </div>
                <div class="flex gap-2">
                    <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1" 
                            class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        Previous
                    </button>
                    <button @click="goToPage(currentPage + 1)" :disabled="currentPage === lastPage"
                            class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add Modal -->
    <div x-cloak x-show="showAddModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Add New Salary</h3>
            </div>
            <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Form Section -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Teacher</label>
                        <select x-model="newSalary.teacher_id" @change="updateAddVoucherPreview()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Teacher</option>
                            <template x-for="teacher in teachers" :key="teacher.id">
                                <option :value="teacher.id" x-text="teacher.name"></option>
                            </template>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                            <input type="number" step="0.01" x-model="newSalary.amount" @input="updateAddVoucherPreview()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter amount">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                            <input type="date" x-model="newSalary.salary_date" @input="updateAddVoucherPreview()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea x-model="newSalary.notes" @input="updateAddVoucherPreview()" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter notes (optional)"></textarea>
                    </div>
                </div>

                <!-- Voucher Preview Section -->
                <div class="border-2 border-gray-300 p-3 bg-white" id="add-salary-voucher">
                    <div class="text-center mb-2">
                        <h2 class="text-sm font-bold text-gray-800">Tr. Khaing Learning Center</h2>
                        <p class="text-xs text-gray-600">Room No. (8,9,10) Padamyar Street,<br>Forest Quarter, Taunggyi.</p>
                        <p class="text-xs text-gray-500">ph: 09428311721, 09253947310, 09429911919</p>
                    </div>

                    <div class="grid grid-cols-2 gap-1 mb-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-0.5">Voucher No:</label>
                            <div class="w-full px-1 py-0.5 border border-gray-300 rounded bg-gray-50 text-xs" x-text="addVoucherNumber"></div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-0.5">Date:</label>
                            <div class="w-full px-1 py-0.5 border border-gray-300 rounded bg-gray-50 text-xs" x-text="newSalary.salary_date"></div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="block text-xs font-medium text-gray-700 mb-0.5">Teacher Name:</label>
                        <div class="w-full px-1 py-0.5 border border-gray-300 rounded bg-gray-50 text-xs" x-text="getTeacherName(newSalary.teacher_id)"></div>
                    </div>

                    <div class="mb-2">
                        <label class="block text-xs font-medium text-gray-700 mb-0.5">Notes:</label>
                        <div class="w-full px-1 py-0.5 border border-gray-300 rounded bg-gray-50 text-xs" x-text="newSalary.notes || '-'"></div>
                    </div>

                    <div class="mb-2">
                        <table class="w-full border-collapse border border-gray-300 text-xs">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-300 px-1 py-0.5 text-left text-xs">Description</th>
                                    <th class="border border-gray-300 px-1 py-0.5 text-right text-xs">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="border border-gray-300 px-1 py-0.5 text-xs">Teacher Salary Payment</td>
                                    <td class="border border-gray-300 px-1 py-0.5 text-right text-xs" x-text="parseFloat(newSalary.amount || 0).toFixed(2)"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="border-t-2 border-gray-300 pt-2 mb-2">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-gray-800">Total:</span>
                            <span class="text-sm font-bold text-gray-800" x-text="parseFloat(newSalary.amount || 0).toFixed(2) + ' MMK'"></span>
                        </div>
                    </div>

                    <div class="border-t border-dashed border-gray-300 pt-2">
                        <p class="text-xs text-gray-600 text-center">သင်တန်းကြေးများကို လ၏ပထမအပတ်တွင် ပေးသွင်းပေးပါရန်။</p>
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                <button @click="showAddModal = false; newSalary = {teacher_id: '', amount: '', salary_date: new Date().toISOString().split('T')[0], notes: ''}; addVoucherNumber = ''" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</button>
                <button @click="printAndAddSalary()" class="px-4 py-2 rounded bg-green-600 hover:bg-green-700 text-white">
                    <i class="fas fa-print mr-2"></i>Print & Add
                </button>
                <button @click="addSalary()" class="px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white">Add Salary</button>
            </div>
        </div>
    </div>
    
    <!-- Edit Modal -->
    <div x-cloak x-show="showEditModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Edit Salary</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Teacher</label>
                    <select x-model="editSalaryData.teacher_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Teacher</option>
                        <template x-for="teacher in teachers" :key="teacher.id">
                            <option :value="teacher.id" x-text="teacher.name"></option>
                        </template>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                        <input type="number" step="0.01" x-model="editSalaryData.amount" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter amount">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                        <input type="date" x-model="editSalaryData.salary_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea x-model="editSalaryData.notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter notes (optional)"></textarea>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                <button @click="showEditModal = false; editSalaryData = {teacher_id: '', amount: '', salary_date: new Date().toISOString().split('T')[0], notes: ''}; editingSalary = null" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</button>
                <button @click="updateSalary()" class="px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white">Update Salary</button>
            </div>
        </div>
    </div>

    <!-- Voucher Modal -->
    <div x-cloak x-show="showVoucherModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">Teacher Salary Voucher</h3>
                <button @click="showVoucherModal = false" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6" id="teacher-salary-voucher">
                <div class="border-2 border-gray-300 p-3 bg-white">
                    <div class="text-center mb-2">
                        <h2 class="text-sm font-bold text-gray-800">Tr. Khaing Learning Center</h2>
                        <p class="text-xs text-gray-600">Room No. (8,9,10) Padamyar Street,<br>Forest Quarter, Taunggyi.</p>
                        <p class="text-xs text-gray-500">ph: 09428311721, 09253947310, 09429911919</p>
                    </div>

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

                    <div class="mb-2">
                        <label class="block text-xs font-medium text-gray-700 mb-0.5">Teacher Name:</label>
                        <div class="w-full px-1 py-0.5 border border-gray-300 rounded bg-gray-50 text-xs" x-text="selectedSalary?.teacher?.name"></div>
                    </div>

                    <div class="mb-2">
                        <label class="block text-xs font-medium text-gray-700 mb-0.5">Notes:</label>
                        <div class="w-full px-1 py-0.5 border border-gray-300 rounded bg-gray-50 text-xs" x-text="selectedSalary?.notes || '-'"></div>
                    </div>

                    <div class="mb-2">
                        <table class="w-full border-collapse border border-gray-300 text-xs">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-300 px-1 py-0.5 text-left text-xs">Description</th>
                                    <th class="border border-gray-300 px-1 py-0.5 text-right text-xs">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="border border-gray-300 px-1 py-0.5 text-xs">Teacher Salary Payment</td>
                                    <td class="border border-gray-300 px-1 py-0.5 text-right text-xs" x-text="parseFloat(selectedSalary?.amount).toFixed(2)"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="border-t-2 border-gray-300 pt-2 mb-2">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-gray-800">Total:</span>
                            <span class="text-sm font-bold text-gray-800" x-text="parseFloat(selectedSalary?.amount).toFixed(2) + ' MMK'"></span>
                        </div>
                    </div>

                    <div class="border-t border-dashed border-gray-300 pt-2">
                        <p class="text-xs text-gray-600 text-center">သင်တန်းကြေးများကို လ၏ပထမအပတ်တွင် ပေးသွင်းပေးပါရန်။</p>
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                <button @click="showVoucherModal = false" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</button>
                <button @click="printAndSaveVoucher()" class="px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white">
                    <i class="fas fa-print mr-2"></i>Print & Save
                </button>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #teacher-salary-voucher, #teacher-salary-voucher *, #add-salary-voucher, #add-salary-voucher * {
        visibility: visible;
    }
    #teacher-salary-voucher, #add-salary-voucher {
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