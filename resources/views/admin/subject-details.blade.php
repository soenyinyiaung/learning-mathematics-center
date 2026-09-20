@extends('admin.layout')

@section('content')
<script>
    window.subjectId = {{ $subjectId }};
</script>
<div x-cloak x-data="subjectDetails()" x-init="init()" class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.subjects') }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h3 class="text-lg font-semibold text-gray-800">Subject Details</h3>
            </div>
            <div class="flex gap-2">
                <button @click="editSubject()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit Subject
                </button>
            </div>
        </div>
        
        <div class="p-6 space-y-6">
            <div x-show="loading" class="text-center py-8 text-gray-500">
                Loading...
            </div>
            
            <div x-show="!loading">
                <!-- Subject Info -->
                <div class="mb-6 flex items-center gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subject Name</label>
                        <p class="text-gray-900 font-medium text-lg" x-text="subject?.name"></p>
                    </div>
                    <div class="ml-auto">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Grade</label>
                        <select x-model="selectedGrade" @change="filterByGrade()" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Grades</option>
                            <template x-for="grade in grades" :key="grade.id">
                                <option :value="grade.id" x-text="grade.name"></option>
                            </template>
                        </select>
                    </div>
                </div>
                
                <!-- Tabs -->
                <div class="border-t border-gray-200 pt-6">
                    <div class="border-b border-gray-200">
                        <nav class="flex -mb-px">
                            <button @click="activeTab = 'students'" 
                                    :class="activeTab === 'students' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="px-4 py-3 border-b-2 font-medium text-sm">
                                Students (<span x-text="students.length"></span>)
                            </button>
                            <button @click="activeTab = 'teachers'" 
                                    :class="activeTab === 'teachers' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="px-4 py-3 border-b-2 font-medium text-sm">
                                Teachers (<span x-text="teachers.length"></span>)
                            </button>
                        </nav>
                    </div>
                    
                    <!-- Students Tab -->
                    <div x-show="activeTab === 'students'" class="pt-4">
                        <div x-show="loadingStudents" class="text-center py-4 text-gray-500">
                            Loading students...
                        </div>
                        <div x-show="!loadingStudents && students.length > 0">
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b border-gray-200">
                                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Student ID</th>
                                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Name</th>
                                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Phone</th>
                                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Grade</th>
                                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="student in students" :key="student.id">
                                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                                <td class="py-3 px-4 text-gray-600" x-text="student.student_id"></td>
                                                <td class="py-3 px-4 text-gray-800 font-medium" x-text="student.name"></td>
                                                <td class="py-3 px-4 text-gray-600" x-text="student.phone"></td>
                                                <td class="py-3 px-4 text-gray-600" x-text="student.grade ? student.grade.name : 'N/A'"></td>
                                                <td class="py-3 px-4 text-gray-600">
                                                    <span class="px-2 py-1 rounded text-xs font-medium" 
                                                          :class="student.status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                                        <span x-text="student.status ? 'Active' : 'Inactive'"></span>
                                                    </span>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Student Pagination -->
                            <div x-show="studentTotalPages > 1" class="mt-4 flex items-center justify-between">
                                <div class="text-sm text-gray-600">
                                    Page <span x-text="studentCurrentPage"></span> of <span x-text="studentTotalPages"></span>
                                </div>
                                <div class="flex gap-2">
                                    <button @click="goToStudentPage(studentCurrentPage - 1)" :disabled="studentCurrentPage === 1" 
                                            class="px-3 py-1 rounded border border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed text-sm">
                                        Previous
                                    </button>
                                    <button @click="goToStudentPage(studentCurrentPage + 1)" :disabled="studentCurrentPage === studentTotalPages"
                                            class="px-3 py-1 rounded border border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed text-sm">
                                        Next
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div x-show="!loadingStudents && students.length === 0" class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg">
                            <i class="fas fa-user-graduate text-4xl text-gray-300 mb-2"></i>
                            <p>No students taking this subject</p>
                        </div>
                    </div>
                    
                    <!-- Teachers Tab -->
                    <div x-show="activeTab === 'teachers'" class="pt-4">
                        <div x-show="loadingTeachers" class="text-center py-4 text-gray-500">
                            Loading teachers...
                        </div>
                        <div x-show="!loadingTeachers && teachers.length > 0">
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b border-gray-200">
                                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Teacher ID</th>
                                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Name</th>
                                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Phone</th>
                                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Employment Type</th>
                                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="teacher in teachers" :key="teacher.id">
                                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                                <td class="py-3 px-4 text-gray-600" x-text="teacher.teacher_id"></td>
                                                <td class="py-3 px-4 text-gray-800 font-medium" x-text="teacher.name"></td>
                                                <td class="py-3 px-4 text-gray-600" x-text="teacher.phone"></td>
                                                <td class="py-3 px-4 text-gray-600">
                                                    <span class="px-2 py-1 rounded text-xs font-medium" 
                                                          :class="teacher.employment_type === 'full-time' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'">
                                                        <span x-text="teacher.employment_type === 'full-time' ? 'Full-Time' : 'Part-Time'"></span>
                                                    </span>
                                                </td>
                                                <td class="py-3 px-4 text-gray-600">
                                                    <span class="px-2 py-1 rounded text-xs font-medium" 
                                                          :class="teacher.status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                                        <span x-text="teacher.status ? 'Active' : 'Inactive'"></span>
                                                    </span>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Teacher Pagination -->
                            <div x-show="teacherTotalPages > 1" class="mt-4 flex items-center justify-between">
                                <div class="text-sm text-gray-600">
                                    Page <span x-text="teacherCurrentPage"></span> of <span x-text="teacherTotalPages"></span>
                                </div>
                                <div class="flex gap-2">
                                    <button @click="goToTeacherPage(teacherCurrentPage - 1)" :disabled="teacherCurrentPage === 1" 
                                            class="px-3 py-1 rounded border border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed text-sm">
                                        Previous
                                    </button>
                                    <button @click="goToTeacherPage(teacherCurrentPage + 1)" :disabled="teacherCurrentPage === teacherTotalPages"
                                            class="px-3 py-1 rounded border border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed text-sm">
                                        Next
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div x-show="!loadingTeachers && teachers.length === 0" class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg">
                            <i class="fas fa-chalkboard-teacher text-4xl text-gray-300 mb-2"></i>
                            <p>No teachers teaching this subject</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Edit Modal -->
    <div x-cloak x-show="showEditModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Edit Subject</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Subject Name</label>
                    <input type="text" x-model="editSubjectData.name" @keyup.enter="updateSubject()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter subject name">
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                <button @click="showEditModal = false; editSubjectData = {name: ''}" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</button>
                <button @click="updateSubject()" class="px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white">Update Subject</button>
            </div>
        </div>
    </div>
</div>
@endsection