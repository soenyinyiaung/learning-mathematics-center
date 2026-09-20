@extends('admin.layout')

@section('content')
<div x-cloak x-data="expenseManagement()" x-init="init()" class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800" x-text="activeTab === 'expenses' ? 'Expenses' : 'Categories'"></h3>
            <button x-show="activeTab === 'expenses'" @click="showAddModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>Add New Expense
            </button>
            <button x-show="activeTab === 'category'" @click="showCategoryModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>Add New Category
            </button>
        </div>
        
        <!-- Tabs -->
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button @click="setTab('expenses')" 
                        :class="activeTab === 'expenses' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-4 py-3 border-b-2 font-medium text-sm">
                    Expenses
                </button>
                <button @click="setTab('category')" 
                        :class="activeTab === 'category' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-4 py-3 border-b-2 font-medium text-sm">
                    Category
                </button>
            </nav>
        </div>
        
        <div class="p-6">
            <!-- Expenses Tab Content -->
            <div x-show="activeTab === 'expenses'">
                <div x-show="loading" class="text-center py-8 text-gray-500">
                    Loading...
                </div>
                
                <div x-show="!loading" class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">ID</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Amount</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Date</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Category</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Notes</th>
                                <th class="text-right py-3 px-4 font-semibold text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="expense in expenses" :key="expense.id">
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-3 px-4 text-gray-600" x-text="expense.id"></td>
                                    <td class="py-3 px-4 text-gray-800 font-semibold" x-text="formatAmount(expense.amount)"></td>
                                    <td class="py-3 px-4 text-gray-600" x-text="formatDate(expense.expense_date)"></td>
                                    <td class="py-3 px-4 text-gray-600" x-text="expense.category ? expense.category.name : '-'"></td>
                                    <td class="py-3 px-4 text-gray-600 text-sm max-w-xs truncate" x-text="expense.notes || '-'"></td>
                                    <td class="py-3 px-4 text-right">
                                        <button @click="editExpense(expense)" class="text-indigo-600 hover:text-indigo-800 mr-3 cursor-pointer">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button @click="deleteExpense(expense.id)" class="text-red-600 hover:text-red-800 cursor-pointer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="expenses.length === 0">
                                <td colspan="6" class="py-8 text-center text-gray-500">No expenses found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div x-show="!loading && expenses.length > 0" class="mt-6 flex items-center justify-between">
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
            
            <!-- Category Tab Content -->
            <div x-show="activeTab === 'category'">
                <div x-show="categoryLoading" class="text-center py-8 text-gray-500">
                    Loading...
                </div>
                
                <div x-show="!categoryLoading" class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">ID</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Category Name</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Expense Count</th>
                                <th class="text-right py-3 px-4 font-semibold text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="category in categories" :key="category.id">
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-3 px-4 text-gray-600" x-text="category.id"></td>
                                    <td class="py-3 px-4 text-gray-800 font-medium" x-text="category.name"></td>
                                    <td class="py-3 px-4 text-gray-600" x-text="getCategoryExpenseCount(category.id)"></td>
                                    <td class="py-3 px-4 text-right">
                                        <button @click="editCategory(category)" class="text-indigo-600 hover:text-indigo-800 mr-3 cursor-pointer">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button @click="deleteCategory(category.id)" class="text-red-600 hover:text-red-800 cursor-pointer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="categories.length === 0">
                                <td colspan="4" class="py-8 text-center text-gray-500">No categories found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add Modal -->
    <div x-cloak x-show="showAddModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Add New Expense</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                        <input type="number" step="0.01" x-model="newExpense.amount" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter amount">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                        <input type="date" x-model="newExpense.expense_date" @click="if(!newExpense.expense_date) newExpense.expense_date = new Date().toISOString().split('T')[0]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select x-model="newExpense.category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">No Category</option>
                        <template x-for="category in categories" :key="category.id">
                            <option :value="category.id" x-text="category.name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea x-model="newExpense.notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter notes (optional)"></textarea>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                <button @click="showAddModal = false; newExpense = {description: '', amount: '', expense_date: '', category_id: '', notes: ''}" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</button>
                <button @click="addExpense()" class="px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white">Add Expense</button>
            </div>
        </div>
    </div>
    
    <!-- Edit Modal -->
    <div x-cloak x-show="showEditModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Edit Expense</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                        <input type="number" step="0.01" x-model="editExpenseData.amount" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter amount">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                        <input type="date" x-model="editExpenseData.expense_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select x-model="editExpenseData.category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">No Category</option>
                        <template x-for="category in categories" :key="category.id">
                            <option :value="category.id" x-text="category.name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea x-model="editExpenseData.notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter notes (optional)"></textarea>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                <button @click="showEditModal = false; editExpenseData = {description: '', amount: '', expense_date: '', category_id: '', notes: ''}; editingExpense = null" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</button>
                <button @click="updateExpense()" class="px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white">Update Expense</button>
            </div>
        </div>
    </div>
    
    <!-- Add Category Modal -->
    <div x-cloak x-show="showCategoryModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Add New Category</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category Name</label>
                    <input type="text" x-model="newCategoryName" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter category name">
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                <button @click="showCategoryModal = false; newCategoryName = ''" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</button>
                <button @click="addCategory()" class="px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white">Add Category</button>
            </div>
        </div>
    </div>
    
    <!-- Edit Category Modal -->
    <div x-cloak x-show="showEditCategoryModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Edit Category</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category Name</label>
                    <input type="text" x-model="editCategoryName" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter category name">
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                <button @click="showEditCategoryModal = false; editCategoryName = ''; editingCategory = null" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</button>
                <button @click="updateCategory()" class="px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white">Update Category</button>
            </div>
        </div>
    </div>
</div>
@endsection