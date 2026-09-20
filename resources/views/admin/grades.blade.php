@extends('admin.layout')

@section('content')
<div x-cloak x-data="gradeManagement()" x-init="init()" class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Grades</h3>
            <button @click="showAddModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>Add New Grade
            </button>
        </div>
        
        <div class="p-6">
            <div x-show="loading" class="text-center py-8 text-gray-500">
                Loading...
            </div>
            
            <div x-show="!loading" class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">ID</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Name</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Created At</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="grade in grades" :key="grade.id">
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-3 px-4 text-gray-600" x-text="grade.id"></td>
                                <td class="py-3 px-4 text-gray-800 font-medium" x-text="grade.name"></td>
                                <td class="py-3 px-4 text-gray-600" x-text="new Date(grade.created_at).toLocaleDateString()"></td>
                                <td class="py-3 px-4 text-right">
                                    <button @click="editGrade(grade)" class="text-indigo-600 hover:text-indigo-800 mr-3 cursor-pointer">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button @click="deleteGrade(grade.id)" class="text-red-600 hover:text-red-800 cursor-pointer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div x-show="!loading && grades.length > 0" class="mt-6 flex items-center justify-between">
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
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Add New Grade</h3>
            </div>
            <div class="p-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Grade Name</label>
                <input type="text" x-model="newGradeName" @keyup.enter="addGrade()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter grade name">
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                <button @click="showAddModal = false; newGradeName = ''" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</button>
                <button @click="addGrade()" class="px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white">Add Grade</button>
            </div>
        </div>
    </div>
    
    <!-- Edit Modal -->
    <div x-cloak x-show="showEditModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Edit Grade</h3>
            </div>
            <div class="p-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Grade Name</label>
                <input type="text" x-model="editGradeName" @keyup.enter="updateGrade()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter grade name">
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                <button @click="showEditModal = false; editGradeName = ''; editingGrade = null" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</button>
                <button @click="updateGrade()" class="px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white">Update Grade</button>
            </div>
        </div>
    </div>
</div>
@endsection