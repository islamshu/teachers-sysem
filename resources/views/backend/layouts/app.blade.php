<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة الإدارة</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">

<nav class="bg-gray-900 shadow">
    <div class="max-w-7xl mx-auto px-4">

        <div class="flex items-center justify-between h-16">

            <div class="flex items-center gap-6">

                <a href="{{ route('admin.teachers.index') }}"
                   class="text-white font-bold text-lg">
                    لوحة الإدارة
                </a>

                <a href="{{ route('admin.teachers.index') }}"
                   class="text-gray-300 hover:text-white">
                    المدرسين
                </a>

            </div>

            <div class="flex items-center gap-4">

                <span class="text-gray-300 text-sm">
                    {{ auth()->user()->name }}
                </span>

                <a href="{{ route('dashboard') }}"
                   class="text-gray-300 hover:text-white text-sm">
                    الموقع
                </a>

            </div>

        </div>

    </div>
</nav>

<main class="py-8">
    <div class="max-w-7xl mx-auto px-4">
        @yield('content')
    </div>
</main>

</body>
</html>