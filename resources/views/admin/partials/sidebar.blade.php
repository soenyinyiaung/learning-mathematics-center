<aside x-cloak 
      x-show="sidebarOpen || !sidebarHidden" 
      x-transition:enter="transition ease-out duration-300"
      x-transition:enter-start="-translate-x-full opacity-0"
      x-transition:enter-end="translate-x-0 opacity-100"
      x-transition:leave="transition ease-in duration-300"
      x-transition:leave-start="translate-x-0 opacity-100"
      x-transition:leave-end="-translate-x-full opacity-0"
      :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
      class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 text-white lg:relative lg:translate-x-0 flex flex-col overflow-hidden shrink-0">
    <div class="p-4 flex items-center justify-between border-b border-gray-700">
        <h1 class="text-xl font-bold mb-0 whitespace-nowrap">Admin Panel</h1>
        <button @click="toggleSidebarOpen()" class="lg:hidden text-gray-300 hover:text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
    
    <div class="flex-1 px-4 overflow-y-auto">
        <nav class="space-y-2">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2 rounded {{ request()->routeIs('admin.dashboard') ? 'bg-blue-900' : 'hover:bg-gray-800 text-gray-300' }} transition-colors">
                <svg class="w-5 h-5 flex-shrink-0 text-blue-500 hover:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="truncate whitespace-nowrap">Dashboard</span>
            </a>
            <a href="{{ route('admin.students') }}" class="flex items-center gap-3 px-4 py-2 rounded {{ request()->routeIs('admin.students') ? 'bg-green-900' : 'hover:bg-gray-800 text-gray-300' }} transition-colors">
                <svg class="w-5 h-5 flex-shrink-0 text-green-500 hover:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span class="truncate whitespace-nowrap">Students List</span>
            </a>
            <a href="{{ route('admin.student-registrations') }}" class="flex items-center gap-3 px-4 py-2 rounded {{ request()->routeIs('admin.student-registrations') ? 'bg-purple-900' : 'hover:bg-gray-800 text-gray-300' }} transition-colors">
                <svg class="w-5 h-5 flex-shrink-0 text-purple-500 hover:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="truncate whitespace-nowrap">Student Registrations</span>
            </a>
            <a href="{{ route('admin.academic-years') }}" class="flex items-center gap-3 px-4 py-2 rounded {{ request()->routeIs('admin.academic-years') ? 'bg-orange-900' : 'hover:bg-gray-800 text-gray-300' }} transition-colors">
                <svg class="w-5 h-5 flex-shrink-0 text-orange-500 hover:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span class="truncate whitespace-nowrap">Academic Years</span>
            </a>
            <a href="{{ route('admin.student-fees') }}" class="flex items-center gap-3 px-4 py-2 rounded {{ request()->routeIs('admin.student-fees') ? 'bg-yellow-900' : 'hover:bg-gray-800 text-gray-300' }} transition-colors">
                <svg class="w-5 h-5 flex-shrink-0 text-yellow-500 hover:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="truncate whitespace-nowrap">Student Fees</span>
            </a>
            <a href="{{ route('admin.teachers') }}" class="flex items-center gap-3 px-4 py-2 rounded {{ request()->routeIs('admin.teachers') ? 'bg-red-900' : 'hover:bg-gray-800 text-gray-300' }} transition-colors">
                <svg class="w-5 h-5 flex-shrink-0 text-red-500 hover:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span class="truncate whitespace-nowrap">Teachers List</span>
            </a>
            <a href="{{ route('admin.teacher-salaries') }}" class="flex items-center gap-3 px-4 py-2 rounded {{ request()->routeIs('admin.teacher-salaries') ? 'bg-pink-900' : 'hover:bg-gray-800 text-gray-300' }} transition-colors">
                <svg class="w-5 h-5 flex-shrink-0 text-pink-500 hover:text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="truncate whitespace-nowrap">Teacher Salaries</span>
            </a>
            <a href="{{ route('admin.expenses') }}" class="flex items-center gap-3 px-4 py-2 rounded {{ request()->routeIs('admin.expenses') ? 'bg-indigo-900' : 'hover:bg-gray-800 text-gray-300' }} transition-colors">
                <svg class="w-5 h-5 flex-shrink-0 text-indigo-500 hover:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span class="truncate whitespace-nowrap">Expenses</span>
            </a>
            <a href="{{ route('admin.sales') }}" class="flex items-center gap-3 px-4 py-2 rounded {{ request()->routeIs('admin.sales') ? 'bg-teal-900' : 'hover:bg-gray-800 text-gray-300' }} transition-colors">
                <svg class="w-5 h-5 flex-shrink-0 text-teal-500 hover:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span class="truncate whitespace-nowrap">Sales</span>
            </a>
            <a href="{{ route('admin.vouchers') }}" class="flex items-center gap-3 px-4 py-2 rounded {{ request()->routeIs('admin.vouchers') ? 'bg-cyan-900' : 'hover:bg-gray-800 text-gray-300' }} transition-colors">
                <svg class="w-5 h-5 flex-shrink-0 text-cyan-500 hover:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="truncate whitespace-nowrap">Vouchers</span>
            </a>
            <a href="{{ route('admin.subjects') }}" class="flex items-center gap-3 px-4 py-2 rounded {{ request()->routeIs('admin.subjects') ? 'bg-lime-900' : 'hover:bg-gray-800 text-gray-300' }} transition-colors">
                <svg class="w-5 h-5 flex-shrink-0 text-lime-500 hover:text-lime-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <span class="truncate whitespace-nowrap">Subjects</span>
            </a>
            <a href="{{ route('admin.grades') }}" class="flex items-center gap-3 px-4 py-2 rounded {{ request()->routeIs('admin.grades') ? 'bg-amber-900' : 'hover:bg-gray-800 text-gray-300' }} transition-colors">
                <svg class="w-5 h-5 flex-shrink-0 text-amber-500 hover:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="truncate whitespace-nowrap">Grades</span>
            </a>
            <a href="{{ route('admin.grade-subject-fees') }}" class="flex items-center gap-3 px-4 py-2 rounded {{ request()->routeIs('admin.grade-subject-fees') ? 'bg-rose-900' : 'hover:bg-gray-800 text-gray-300' }} transition-colors">
                <i class="fas fa-tags w-5 h-5 flex-shrink-0 text-center text-rose-500 hover:text-rose-400"></i>
                <span class="truncate whitespace-nowrap">Grade Subject Fees</span>
            </a>
            <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-4 py-2 rounded {{ request()->routeIs('admin.users') ? 'bg-violet-900' : 'hover:bg-gray-800 text-gray-300' }} transition-colors">
                <svg class="w-5 h-5 flex-shrink-0 text-violet-500 hover:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span class="truncate whitespace-nowrap">Users</span>
            </a>
            <a href="{{ route('admin.settings') }}" class="flex items-center gap-3 px-4 py-2 rounded {{ request()->routeIs('admin.settings') ? 'bg-slate-900' : 'hover:bg-gray-800 text-gray-300' }} transition-colors">
                <i class="fas fa-cog w-5 h-5 flex-shrink-0 text-center text-slate-500 hover:text-slate-400"></i>
                <span class="truncate whitespace-nowrap">Settings</span>
            </a>
        </nav>
    </div>
</aside>

<!-- Mobile overlay -->
<div x-cloak x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="toggleSidebarOpen()" class="fixed inset-0 z-40 lg:hidden bg-black/50"></div>