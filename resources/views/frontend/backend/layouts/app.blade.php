<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>لوحة الإدارة</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100">

    <nav class="bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="{{ route('admin.teachers.index') }}" class="font-bold">
                لوحة الإدارة
            </a>

            <a href="{{ route('dashboard') }}" class="text-sm text-gray-300 hover:text-white">
                رجوع للموقع
            </a>
        </div>
    </nav>

    <main class="py-8">
        <div class="max-w-7xl mx-auto px-4">
            @yield('content')
        </div>
    </main>

</body>

</html>
