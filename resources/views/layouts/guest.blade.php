<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Teachers') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=tajawal:400,500,700,800&display=swap" rel="stylesheet" />

        <!-- Styles only (no Inertia JS - auth pages are Blade) -->
        @vite(['resources/css/app.css'])
    </head>
    <body class="font-sans antialiased bg-surface-50">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <!-- Background decoration -->
            <div class="fixed inset-0 pointer-events-none overflow-hidden">
                <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary-500/10 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-warm-500/10 rounded-full blur-3xl"></div>
            </div>

            <!-- Logo -->
            <div class="relative mb-8">
                <a href="/" class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-700 rounded-2xl flex items-center justify-center shadow-lg shadow-primary-500/20">
                        <span class="text-white font-extrabold text-xl">م</span>
                    </div>
                    <span class="text-2xl font-extrabold bg-gradient-to-l from-primary-600 to-primary-800 bg-clip-text text-transparent">
                        {{ config('app.name', 'Teachers') }}
                    </span>
                </a>
            </div>

            <!-- Card -->
            <div class="relative w-full sm:max-w-md px-6 sm:px-0">
                <div class="card p-8 animate-fade-in-up">
                    {{ $slot }}
                </div>

                <!-- Footer link -->
                <p class="text-center mt-6 text-sm text-slate-500">
                    @if (Route::is('login'))
                        ليس لديك حساب؟
                        <a href="{{ route('register') }}" class="font-semibold text-primary-600 hover:text-primary-700 transition-colors">
                            سجل الآن
                        </a>
                    @elseif (Route::is('register'))
                        لديك حساب بالفعل؟
                        <a href="{{ route('login') }}" class="font-semibold text-primary-600 hover:text-primary-700 transition-colors">
                            تسجيل الدخول
                        </a>
                    @endif
                </p>
            </div>
        </div>
    </body>
</html>
