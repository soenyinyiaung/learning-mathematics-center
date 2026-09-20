<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin' }} - Learning Mathematics Center</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">
    <div x-data="{
        sidebarOpen: false,
        sidebarHidden: true, // Default to hidden
        
        toggleSidebarOpen() {
            this.sidebarOpen = !this.sidebarOpen;
        },
        
        toggleSidebarHidden() {
            this.sidebarHidden = !this.sidebarHidden;
            localStorage.setItem('sidebarHidden', this.sidebarHidden);
            // Update Alpine store
            Alpine.store('sidebar', { hidden: this.sidebarHidden });
        },
        
        init() {
            // Load sidebar state from localStorage, default to hidden if not set
            const savedState = localStorage.getItem('sidebarHidden');
            this.sidebarHidden = savedState === 'true' || savedState === null;
            this.sidebarOpen = false; // Mobile sidebar should be closed by default
            // Initialize Alpine store
            Alpine.store('sidebar', { hidden: this.sidebarHidden });
        }
    }" x-init="init()" x-cloak class="flex h-screen">
        @include('admin.partials.sidebar')
        
        <div class="flex-1 flex flex-col overflow-hidden lg:transition-all lg:duration-300 lg:ease-in-out">
            @include('admin.partials.header')
            
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>
            
            @include('admin.partials.footer')
        </div>
    </div>
</body>
</html>