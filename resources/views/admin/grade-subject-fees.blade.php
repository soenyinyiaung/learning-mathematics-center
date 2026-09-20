@extends('admin.layout')

@section('content')
<div x-cloak x-data="gradeSubjectFeeManagement()" x-init="init()" class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Grade Subject Fees</h3>
            <button @click="showAddModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>Add New Fee
            </button>
        </div>
        
        <div class="p-6">
            <!-- Filters -->
            <div class="mb-6 flex gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Grade Filter</label>
                    <select x-model="filterGrade" @change="filterFees()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Grades</option>
                        <template x-for="grade in grades" :key="grade.id">
                            <option :value="grade.id" x-text="grade.name"></option>
                        </template>
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Subject Filter</label>
                    <select x-model="filterSubject" @change="filterFees()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Subjects</option>
                        <template x-for="subject in subjects" :key="subject.id">
                            <option :value="subject.id" x-text="subject.name"></option>
                        </template>
                    </select>
                </div>
            </div>
            
            <div x-show="loading" class="text-center py-8 text-gray-500">
                Loading...
            </div>
            
            <div x-show="!loading" class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Grade</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Subject</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Fee (MMK)</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="fee in fees" :key="fee.id">
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-3 px-4 text-gray-800 font-medium" x-text="fee.grade ? fee.grade.name : 'N/A'"></td>
                                <td class="py-3 px-4 text-gray-800 font-medium" x-text="fee.subject ? fee.subject.name : 'N/A'"></td>
                                <td class="py-3 px-4 text-gray-600">
                                    <span x-text="Number(fee.fee).toLocaleString() + ' MMK'"></span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <button @click="editFee(fee)" class="text-indigo-600 hover:text-indigo-800 mr-3 cursor-pointer">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button @click="deleteFee(fee.id)" class="text-red-600 hover:text-red-800 cursor-pointer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div x-show="!loading && fees.length > 0" class="mt-6 flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    Page <span x-text="currentPage"></span> of <span x-text="lastPage"></span>
                </div>
                <div class="flex gap-2">
                    <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1" 
                            class="px-3 py-1 rounded border border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed text-sm">
                        Previous
                    </button>
                    <button @click="goToPage(currentPage + 1)" :disabled="currentPage === lastPage"
                            class="px-3 py-1 rounded border border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed text-sm">
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add Modal -->
    <div x-cloak x-show="showAddModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Add Grade Subject Fee</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Grade</label>
                    <select x-model="newFee.grade_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Grade</option>
                        <template x-for="grade in grades" :key="grade.id">
                            <option :value="grade.id" x-text="grade.name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                    <select x-model="newFee.subject_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Subject</option>
                        <template x-for="subject in subjects" :key="subject.id">
                            <option :value="subject.id" x-text="subject.name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fee (MMK)</label>
                    <input type="number" x-model="newFee.fee" @keyup.enter="addFee()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter fee" min="0" step="0.01">
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                <button @click="showAddModal = false; newFee = {grade_id: '', subject_id: '', fee: ''}" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</button>
                <button @click="addFee()" class="px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white">Add Fee</button>
            </div>
        </div>
    </div>
    
    <!-- Edit Modal -->
    <div x-cloak x-show="showEditModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Edit Grade Subject Fee</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Grade</label>
                    <select x-model="editFeeData.grade_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Grade</option>
                        <template x-for="grade in grades" :key="grade.id">
                            <option :value="grade.id" x-text="grade.name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                    <select x-model="editFeeData.subject_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Subject</option>
                        <template x-for="subject in subjects" :key="subject.id">
                            <option :value="subject.id" x-text="subject.name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fee (MMK)</label>
                    <input type="number" x-model="editFeeData.fee" @keyup.enter="updateFee()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter fee" min="0" step="0.01">
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                <button @click="showEditModal = false; editFeeData = {grade_id: '', subject_id: '', fee: ''}; editingFee = null" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</button>
                <button @click="updateFee()" class="px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white">Update Fee</button>
            </div>
        </div>
    </div>
</div>
@endsection