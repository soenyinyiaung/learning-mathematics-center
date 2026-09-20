<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hello World - Learning Mathematics Center</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-indigo-500 to-purple-600 min-h-screen flex items-center justify-center">
    <div x-data="{ count: 0 }" class="bg-white p-8 rounded-lg shadow-2xl text-center max-w-md">
        <h1 class="text-3xl font-bold text-gray-800 mb-4">Hello World!</h1>
        <p class="text-gray-600 text-lg mb-6">Welcome to Learning Mathematics Center</p>
        
        <div class="border-t pt-4">
            <p class="text-sm text-gray-400">Powered by <a href="https://soenyinyiaung.site">Soe Nyi Nyi Aung<a></p>
        </div>
    </div>
</body>
</html>