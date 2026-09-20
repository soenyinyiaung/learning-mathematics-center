@extends('admin.layout')

@section('content')
<div x-cloak x-data="studentManagement()" x-init="init()" class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Students List</h3>
            <button @click="showAddModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>Add New Student
            </button>
        </div>
        
        <!-- Tabs -->
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button @click="setTab('active')" 
                        :class="activeTab === 'active' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-4 py-3 border-b-2 font-medium text-sm">
                    Active Students
                </button>
                <button @click="setTab('inactive')" 
                        :class="activeTab === 'inactive' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-4 py-3 border-b-2 font-medium text-sm">
                    Inactive Students
                </button>
            </nav>
        </div>
        
        <div class="p-6">
            <!-- Search -->
            <div class="mb-4">
                <input type="text" x-model="searchQuery" @input="searchStudents()" placeholder="Search by name, student ID, or phone..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div x-show="loading" class="text-center py-8 text-gray-500">
                Loading...
            </div>
            
            <div x-show="!loading" class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">ID</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Student ID</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Name</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Phone</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Birthday</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">NRC</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Grade</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Status</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Guardian</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="student in filteredStudents" :key="student.id">
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-3 px-4 text-gray-600" x-text="student.id"></td>
                                <td class="py-3 px-4 text-gray-800 font-medium" x-text="student.student_id"></td>
                                <td class="py-3 px-4 text-gray-800 font-medium" x-text="student.name"></td>
                                <td class="py-3 px-4 text-gray-600" x-text="student.phone"></td>
                                <td class="py-3 px-4 text-gray-600" x-text="new Date(student.birthday).toLocaleDateString()"></td>
                                <td class="py-3 px-4 text-gray-600" x-text="student.nrc_id"></td>
                                <td class="py-3 px-4 text-gray-600" x-text="student.grade ? student.grade.name : 'N/A'"></td>
                                <td class="py-3 px-4 text-gray-600">
                                    <button @click="toggleStudentStatus(student.id)" class="cursor-pointer hover:text-indigo-600 transition-colors" title="Toggle Status">
                                        <i :class="student.status ? 'fas fa-toggle-on text-green-600' : 'fas fa-toggle-off text-gray-400'"></i>
                                    </button>
                                </td>
                                <td class="py-3 px-4 text-gray-600">
                                    <div x-text="student.guardian_name"></div>
                                    <div class="text-sm text-gray-500" x-text="student.guardian_contact"></div>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <button @click="editStudent(student)" class="text-indigo-600 hover:text-indigo-800 mr-3 cursor-pointer">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button @click="deleteStudent(student.id)" class="text-red-600 hover:text-red-800 cursor-pointer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div x-show="!loading && students.length > 0" class="mt-6 flex items-center justify-between">
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
                <h3 class="text-lg font-semibold text-gray-800">Add New Student</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Student ID</label>
                        <input type="text" x-model="newStudent.student_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter student ID">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                        <input type="text" x-model="newStudent.name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter name">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input type="text" x-model="newStudent.phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter phone">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Birthday</label>
                        <input type="date" x-model="newStudent.birthday" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">NRC ID</label>
                        <input type="text" x-model="newStudent.nrc_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter NRC ID">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Grade</label>
                        <select x-model="newStudent.grade_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Grade</option>
                            <template x-for="grade in grades" :key="grade.id">
                                <option :value="grade.id" x-text="grade.name"></option>
                            </template>
                        </select>
                    </div>
                </div>
                <div class="border-t border-gray-200 pt-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Guardian Information</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Guardian Name</label>
                            <input type="text" x-model="newStudent.guardian_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter guardian name">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Guardian Contact</label>
                            <input type="text" x-model="newStudent.guardian_contact" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter guardian contact">
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                <button @click="showAddModal = false; newStudent = {student_id: '', name: '', phone: '', birthday: '', nrc_id: '', grade_id: '', guardian_name: '', guardian_contact: ''}" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</button>
                <button @click="addStudent()" class="px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white">Add Student</button>
            </div>
        </div>
    </div>
    
    <!-- Edit Modal -->
    <div x-cloak x-show="showEditModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Edit Student</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Student ID</label>
                        <input type="text" x-model="editStudentData.student_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter student ID">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                        <input type="text" x-model="editStudentData.name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter name">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input type="text" x-model="editStudentData.phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter phone">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Birthday</label>
                        <input type="date" x-model="editStudentData.birthday" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">NRC ID</label>
                        <input type="text" x-model="editStudentData.nrc_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter NRC ID">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Grade</label>
                        <select x-model="editStudentData.grade_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Grade</option>
                            <template x-for="grade in grades" :key="grade.id">
                                <option :value="grade.id" x-text="grade.name"></option>
                            </template>
                        </select>
                    </div>
                </div>
                <div class="border-t border-gray-200 pt-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Guardian Information</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Guardian Name</label>
                            <input type="text" x-model="editStudentData.guardian_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter guardian name">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Guardian Contact</label>
                            <input type="text" x-model="editStudentData.guardian_contact" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter guardian contact">
                        </div>
                    </div>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" x-model="editStudentData.status" id="editStudentStatus" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="editStudentStatus" class="ml-2 block text-sm text-gray-900">Active Student</label>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                <button @click="showEditModal = false; editStudentData = {student_id: '', name: '', phone: '', birthday: '', nrc_id: '', grade_id: '', guardian_name: '', guardian_contact: ''}; editingStudent = null" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</button>
                <button @click="updateStudent()" class="px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white">Update Student</button>
            </div>
        </div>
    </div>
</div>
@endsection