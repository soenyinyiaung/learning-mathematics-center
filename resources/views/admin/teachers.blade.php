@extends('admin.layout')

@section('content')
<div x-cloak x-data="teacherManagement()" x-init="init()" class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Teachers List</h3>
            <button @click="showAddModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>Add New Teacher
            </button>
        </div>
        
        <!-- Tabs -->
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button @click="setTab('active')" 
                        :class="activeTab === 'active' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-4 py-3 border-b-2 font-medium text-sm">
                    Active Teachers
                </button>
                <button @click="setTab('inactive')" 
                        :class="activeTab === 'inactive' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-4 py-3 border-b-2 font-medium text-sm">
                    Inactive Teachers
                </button>
            </nav>
        </div>
        
        <div class="p-6">
            <!-- Search -->
            <div class="mb-4">
                <input type="text" x-model="searchQuery" @input="searchTeachers()" placeholder="Search by name, teacher ID, or phone..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div x-show="loading" class="text-center py-8 text-gray-500">
                Loading...
            </div>
            
            <div x-show="!loading" class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">ID</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Teacher ID</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Name</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Phone</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">NRC</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Employment Type</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Subjects</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Status</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="teacher in filteredTeachers" :key="teacher.id">
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-3 px-4 text-gray-600" x-text="teacher.id"></td>
                                <td class="py-3 px-4 text-gray-800 font-medium" x-text="teacher.teacher_id"></td>
                                <td class="py-3 px-4 text-gray-800 font-medium" x-text="teacher.name"></td>
                                <td class="py-3 px-4 text-gray-600" x-text="teacher.phone"></td>
                                <td class="py-3 px-4 text-gray-600" x-text="teacher.nrc_id"></td>
                                <td class="py-3 px-4 text-gray-600">
                                    <span class="px-2 py-1 rounded text-xs font-medium" 
                                          :class="teacher.employment_type === 'full-time' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'">
                                        <span x-text="teacher.employment_type === 'full-time' ? 'Full-Time' : 'Part-Time'"></span>
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-gray-600">
                                    <template x-if="teacher.subjects && teacher.subjects.length > 0">
                                        <div class="flex flex-wrap gap-1">
                                            <template x-for="subject in teacher.subjects" :key="subject.id">
                                                <span class="inline-block bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded" x-text="subject.name"></span>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="!teacher.subjects || teacher.subjects.length === 0">
                                        <span class="text-gray-400">No subjects</span>
                                    </template>
                                </td>
                                <td class="py-3 px-4 text-gray-600">
                                    <button @click="toggleTeacherStatus(teacher.id)" class="cursor-pointer hover:text-indigo-600 transition-colors" title="Toggle Status">
                                        <i :class="teacher.status ? 'fas fa-toggle-on text-green-600' : 'fas fa-toggle-off text-gray-400'"></i>
                                    </button>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <button @click="editTeacher(teacher)" class="text-indigo-600 hover:text-indigo-800 mr-3 cursor-pointer">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button @click="deleteTeacher(teacher.id)" class="text-red-600 hover:text-red-800 cursor-pointer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div x-show="!loading && teachers.length > 0" class="mt-6 flex items-center justify-between">
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
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Add New Teacher</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <input type="text" x-model="newTeacher.name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter name">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input type="text" x-model="newTeacher.phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter phone">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">NRC ID</label>
                        <input type="text" x-model="newTeacher.nrc_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter NRC ID">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Employment Type</label>
                        <select x-model="newTeacher.employment_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="full-time">Full-Time</option>
                            <option value="part-time">Part-Time</option>
                        </select>
                    </div>
                </div>
                <div class="border-t border-gray-200 pt-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Subjects</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <template x-for="subject in subjects" :key="subject.id">
                            <div class="flex items-center">
                                <input type="checkbox" :id="'subject-add-' + subject.id" 
                                       :value="subject.id" 
                                       x-model="newTeacher.subject_ids"
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label :for="'subject-add-' + subject.id" class="ml-2 block text-sm text-gray-900" x-text="subject.name"></label>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                <button @click="showAddModal = false; newTeacher = {name: '', phone: '', nrc_id: '', employment_type: 'full-time', subject_ids: []}" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</button>
                <button @click="addTeacher()" class="px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white">Add Teacher</button>
            </div>
        </div>
    </div>
    
    <!-- Edit Modal -->
    <div x-cloak x-show="showEditModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Edit Teacher</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Teacher ID</label>
                        <input type="text" x-model="editTeacherData.teacher_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                        <input type="text" x-model="editTeacherData.name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter name">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input type="text" x-model="editTeacherData.phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter phone">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">NRC ID</label>
                        <input type="text" x-model="editTeacherData.nrc_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter NRC ID">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Employment Type</label>
                        <select x-model="editTeacherData.employment_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="full-time">Full-Time</option>
                            <option value="part-time">Part-Time</option>
                        </select>
                    </div>
                </div>
                <div class="border-t border-gray-200 pt-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Subjects</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <template x-for="subject in subjects" :key="subject.id">
                            <div class="flex items-center">
                                <input type="checkbox" :id="'subject-edit-' + subject.id" 
                                       :value="subject.id" 
                                       x-model="editTeacherData.subject_ids"
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label :for="'subject-edit-' + subject.id" class="ml-2 block text-sm text-gray-900" x-text="subject.name"></label>
                            </div>
                        </template>
                    </div>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" x-model="editTeacherData.status" id="editTeacherStatus" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="editTeacherStatus" class="ml-2 block text-sm text-gray-900">Active Teacher</label>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                <button @click="showEditModal = false; editTeacherData = {teacher_id: '', name: '', phone: '', nrc_id: '', employment_type: 'full-time', status: true, subject_ids: []}; editingTeacher = null" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</button>
                <button @click="updateTeacher()" class="px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white">Update Teacher</button>
            </div>
        </div>
    </div>
</div>
@endsection