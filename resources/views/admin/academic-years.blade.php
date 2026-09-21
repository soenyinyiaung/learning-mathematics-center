@extends('admin.layout')

@section('content')
<div x-cloak x-data="academicYearManagement()" x-init="init()" class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Academic Years</h3>
            <button @click="showAddModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>Add Academic Year
            </button>
        </div>
        
        <div class="p-6">
            <div x-show="loading" class="text-center py-8 text-gray-500">
                Loading...
            </div>
            
            <div x-show="!loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <template x-for="year in academicYears" :key="year.id">
                    <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow cursor-pointer"
                         @click="selectAcademicYear(year)"
                         :class="{'border-indigo-500 bg-indigo-50': selectedYear && selectedYear.id === year.id}">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-xl font-bold text-gray-800" x-text="year.year_range"></h4>
                            <div class="flex items-center gap-2">
                                <button @click.stop="editYear(year)" class="text-indigo-600 hover:text-indigo-800">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button @click.stop="deleteYear(year.id)" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600">Academic Year</p>
                    </div>
                </template>
            </div>
            
            <div x-show="!loading && academicYears.length === 0" class="text-center py-8 text-gray-500">
                <p>No academic years found. Add your first academic year to get started.</p>
            </div>
        </div>
    </div>

    <!-- Grades View for Selected Academic Year -->
    <div x-show="selectedYear" x-transition class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">
                <span x-text="selectedYear ? selectedYear.year_range : ''"></span> - Grades
            </h3>
            <button @click="selectedYear = null; selectedGrade = null" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="p-6">
            <div x-show="loadingGrades" class="text-center py-8 text-gray-500">
                Loading grades...
            </div>
            
            <div x-show="!loadingGrades" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <template x-for="grade in grades" :key="grade.id">
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer"
                         @click="selectGrade(grade)"
                         :class="{'border-indigo-500 bg-indigo-50': selectedGrade && selectedGrade.id === grade.id}">
                        <h4 class="font-semibold text-gray-800" x-text="grade.name"></h4>
                        <p class="text-sm text-gray-600 mt-2">Click to view students</p>
                    </div>
                </template>
            </div>
            
            <div x-show="!loadingGrades && grades.length === 0" class="text-center py-8 text-gray-500">
                <p>No grades found for this academic year.</p>
            </div>
        </div>
    </div>

    <!-- Students View for Selected Grade -->
    <div x-show="selectedGrade" x-transition class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">
                <span x-text="selectedYear ? selectedYear.year_range : ''"></span> - <span x-text="selectedGrade ? selectedGrade.name : ''"></span> - Students
            </h3>
            <div class="flex items-center gap-3">
                <button @click="showAddStudentModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>Add Student
                </button>
                <button @click="selectedGrade = null" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        
        <div class="p-6">
            <div x-show="loadingStudents" class="text-center py-8 text-gray-500">
                Loading students...
            </div>
            
            <div x-show="!loadingStudents" class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Student ID</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Name</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Phone</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="student in students" :key="student.id">
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-3 px-4 text-gray-800 font-medium" x-text="student.student_id"></td>
                                <td class="py-3 px-4 text-gray-800 font-medium" x-text="student.name"></td>
                                <td class="py-3 px-4 text-gray-600" x-text="student.phone"></td>
                                <td class="py-3 px-4 text-right">
                                    <button @click="removeStudent(student.id)" class="text-red-600 hover:text-red-800 cursor-pointer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            
            <div x-show="!loadingStudents && students.length === 0" class="text-center py-8 text-gray-500">
                <p>No students found for this grade in this academic year.</p>
            </div>
        </div>
    </div>

    <!-- Add Academic Year Modal -->
    <div x-cloak x-show="showAddModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Add Academic Year</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Year</label>
                    <input type="number" x-model="newYear.start_year" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="2026">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Year</label>
                    <input type="number" x-model="newYear.end_year" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="2027">
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                <button @click="showAddModal = false; newYear = {start_year: '', end_year: ''}" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</button>
                <button @click="addYear()" class="px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white">Add Year</button>
            </div>
        </div>
    </div>

    <!-- Edit Academic Year Modal -->
    <div x-cloak x-show="showEditModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Edit Academic Year</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Year</label>
                    <input type="number" x-model="editYearData.start_year" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Year</label>
                    <input type="number" x-model="editYearData.end_year" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                <button @click="showEditModal = false; editYearData = {start_year: '', end_year: ''}; editingYear = null" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</button>
                <button @click="updateYear()" class="px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white">Update Year</button>
            </div>
        </div>
    </div>

    <!-- Add Student Modal -->
    <div x-cloak x-show="showAddStudentModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Add Students to <span x-text="selectedGrade ? selectedGrade.name : ''"></span></h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search Students</label>
                    <input type="text" x-model="studentSearchQuery" @input="searchStudents()" placeholder="Search by name or student ID..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Students</label>
                    <div class="border border-gray-300 rounded-lg p-3 max-h-64 overflow-y-auto">
                        <template x-for="student in filteredAvailableStudents" :key="student.id">
                            <label class="flex items-center py-2 hover:bg-gray-50 px-2 rounded">
                                <input type="checkbox" :value="student.id" x-model="newStudent.student_ids" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700" x-text="student.name + ' (' + student.student_id + ')'"></span>
                            </label>
                        </template>
                        <div x-show="filteredAvailableStudents.length === 0" class="text-center py-4 text-gray-500">
                            No students found
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                <button @click="showAddStudentModal = false; newStudent = {student_ids: []}; studentSearchQuery = ''" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</button>
                <button @click="addStudent()" class="px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white">Add Students</button>
            </div>
        </div>
    </div>
</div>
@endsection