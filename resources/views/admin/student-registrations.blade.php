@extends('admin.layout')

@section('content')
<div x-cloak x-data="studentRegistrationManagement()" x-init="init()" class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Student Registrations</h3>
            <button @click="showAddModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>Add Registration
            </button>
        </div>
        
        <div class="p-6">
            <!-- Search Section -->
            <div class="mb-6">
                <input type="text" x-model="searchQuery" @input="searchRegistrations()" placeholder="Search by student name..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <!-- Registration List -->
            <div x-show="loading" class="text-center py-8 text-gray-500">
                Loading...
            </div>
            
            <div x-show="!loading">
                <div x-show="registrations.length === 0" class="text-center py-8 text-gray-500">
                    <p>No registrations found</p>
                </div>
                
                <div x-show="registrations.length > 0" class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Student</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Grade</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Guardian</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Phone</th>
                                <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="registration in registrations" :key="registration.id">
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-800 font-medium" x-text="registration.name"></td>
                                    <td class="px-4 py-3 text-sm text-gray-600" x-text="registration.grade ? registration.grade.name : 'N/A'"></td>
                                    <td class="px-4 py-3 text-sm text-gray-600" x-text="registration.guardian_name"></td>
                                    <td class="px-4 py-3 text-sm text-gray-600" x-text="registration.phone"></td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex justify-center gap-2">
                                            <button @click="confirmRegistration(registration)" class="text-green-600 hover:text-green-800 cursor-pointer" title="Confirm">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button @click="rejectRegistration(registration)" class="text-red-600 hover:text-red-800 cursor-pointer" title="Reject">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div x-show="registrations.length > 0" class="mt-6 flex items-center justify-between">
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
    </div>
    
    <!-- Add Registration Modal -->
    <div x-cloak x-show="showAddModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Add Student Registration</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input type="text" x-model="newRegistration.name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter full name">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input type="tel" x-model="newRegistration.phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter phone number">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                        <input type="date" x-model="newRegistration.birthday" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">NRC Number</label>
                    <input type="text" x-model="newRegistration.nrc_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter NRC number">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Grade</label>
                    <select x-model="newRegistration.grade_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Grade</option>
                        <template x-for="grade in grades" :key="grade.id">
                            <option :value="grade.id" x-text="grade.name"></option>
                        </template>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Guardian Name</label>
                    <input type="text" x-model="newRegistration.guardian_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter guardian name">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Guardian Contact</label>
                    <input type="tel" x-model="newRegistration.guardian_contact" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter guardian contact">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Subjects</label>
                    <div class="border border-gray-300 rounded-lg p-3 max-h-32 overflow-y-auto">
                        <template x-for="subject in subjects" :key="subject.id">
                            <label class="flex items-center py-1 hover:bg-gray-50 px-2 rounded">
                                <input type="checkbox" :value="subject.id" x-model="newRegistration.subject_ids" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700" x-text="subject.name"></span>
                            </label>
                        </template>
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                <button @click="showAddModal = false; newRegistration = {name: '', phone: '', birthday: '', nrc_id: '', guardian_name: '', guardian_contact: '', subject_ids: []}" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</button>
                <button @click="addRegistration()" class="px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white">Add Registration</button>
            </div>
        </div>
    </div>
</div>
@endsection