@extends('admin.layout')

@section('content')
<div x-cloak x-data="userManagement()" x-init="init()" class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Users</h3>
            <button @click="showAddModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>Add New User
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
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Email</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Phone</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Created At</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="user in users" :key="user.id">
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-3 px-4 text-gray-600" x-text="user.id"></td>
                                <td class="py-3 px-4 text-gray-800 font-medium" x-text="user.name"></td>
                                <td class="py-3 px-4 text-gray-600" x-text="user.email"></td>
                                <td class="py-3 px-4 text-gray-600" x-text="user.phone || '-'"></td>
                                <td class="py-3 px-4 text-gray-600" x-text="formatDate(user.created_at)"></td>
                                <td class="py-3 px-4 text-right">
                                    <button @click="editUser(user)" class="text-indigo-600 hover:text-indigo-800 mr-3 cursor-pointer">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button @click="deleteUser(user.id)" class="text-red-600 hover:text-red-800 cursor-pointer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="users.length === 0">
                            <td colspan="6" class="py-8 text-center text-gray-500">No users found</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div x-show="!loading && users.length > 0" class="mt-6 flex items-center justify-between">
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
                <h3 class="text-lg font-semibold text-gray-800">Add New User</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <input type="text" x-model="newUser.name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter name">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" x-model="newUser.email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter email">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                    <input type="text" x-model="newUser.phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter phone (optional)">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <input :type="showNewPassword ? 'text' : 'password'" x-model="newUser.password" class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter password (min 6 characters)">
                            <button @click="showNewPassword = !showNewPassword" type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                <i :class="showNewPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                            </button>
                        </div>
                        <button @click="generateAndSetPassword('new')" type="button" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-gray-700 transition-colors">
                            <i class="fas fa-key"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                <button @click="showAddModal = false; newUser = {name: '', email: '', phone: '', password: ''}; showNewPassword = false" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</button>
                <button @click="addUser()" class="px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white">Add User</button>
            </div>
        </div>
    </div>
    
    <!-- Edit Modal -->
    <div x-cloak x-show="showEditModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Edit User</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <input type="text" x-model="editUserData.name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter name">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" x-model="editUserData.email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter email">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                    <input type="text" x-model="editUserData.phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter phone (optional)">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <input :type="showEditPassword ? 'text' : 'password'" x-model="editUserData.password" class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter new password (leave blank to keep current)">
                            <button @click="showEditPassword = !showEditPassword" type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                <i :class="showEditPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                            </button>
                        </div>
                        <button @click="generateAndSetPassword('edit')" type="button" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-gray-700 transition-colors">
                            <i class="fas fa-key"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                <button @click="showEditModal = false; editUserData = {name: '', email: '', phone: '', password: ''}; editingUser = null; showEditPassword = false" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</button>
                <button @click="updateUser()" class="px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white">Update User</button>
            </div>
        </div>
    </div>
</div>
@endsection