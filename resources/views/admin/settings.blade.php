@extends('admin.layout')

@section('content')
<div x-cloak x-data="settingManagement()" x-init="init()" class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Settings</h3>
        </div>
        
        <div class="p-6">
            <!-- Change Password Section -->
            <div class="max-w-md">
                <h4 class="text-md font-semibold text-gray-700 mb-4">Change Password</h4>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                        <input type="password" x-model="passwordForm.current_password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter current password">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                        <input type="password" x-model="passwordForm.new_password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter new password (min 8 characters)">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                        <input type="password" x-model="passwordForm.new_password_confirmation" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Confirm new password">
                    </div>
                    
                    <button @click="changePassword()" 
                            :disabled="loading"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white py-3 px-4 rounded-lg transition-colors">
                        <span x-show="!loading">Change Password</span>
                        <span x-show="loading">Changing...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection