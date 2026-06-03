<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            لوحة التحكم
        </h2>
    </x-slot>

    <div class="py-12" dir="rtl">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">
                            أهلاً {{ auth()->user()->name }}
                        </h3>

                        <p class="mt-2 text-gray-600">
                            من هنا يمكنك إدارة بياناتك كمدرس داخل المنصة.
                        </p>
                    </div>

                    <div>
                        @if (auth()->user()->teacherProfile)
                            <a href="{{ route('teacher-profile.edit') }}"
                               class="inline-flex bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg">
                                تعديل بيانات المدرس
                            </a>
                        @else
                            <a href="{{ route('teacher-profile.create') }}"
                               class="inline-flex bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg">
                                إكمال بيانات المدرس
                            </a>
                        @endif
                    </div>

                </div>
            </div>

            @if (auth()->user()->teacherProfile)
                @php $profile = auth()->user()->teacherProfile; @endphp

                <div class="mt-6 bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">

                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            بياناتك الحالية
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div class="flex items-center gap-4">
                                @if ($profile->photo)
                                    <img src="{{ asset('storage/' . $profile->photo) }}"
                                         class="w-20 h-20 rounded-full object-cover border">
                                @else
                                    <div class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-sm">
                                        لا توجد صورة
                                    </div>
                                @endif

                                <div>
                                    <p class="font-semibold text-gray-900">
                                        {{ auth()->user()->name }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ auth()->user()->email }}
                                    </p>
                                </div>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">حالة الحساب</p>

                                @if ($profile->status === 'approved')
                                    <span class="inline-flex mt-1 px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                                        مقبول
                                    </span>
                                @elseif ($profile->status === 'rejected')
                                    <span class="inline-flex mt-1 px-3 py-1 rounded-full text-sm bg-red-100 text-red-800">
                                        مرفوض
                                    </span>
                                @else
                                    <span class="inline-flex mt-1 px-3 py-1 rounded-full text-sm bg-yellow-100 text-yellow-800">
                                        قيد المراجعة
                                    </span>
                                @endif
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">المبحث</p>
                                <p class="font-medium">{{ $profile->subject }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">سنوات الخبرة</p>
                                <p class="font-medium">{{ $profile->experience_years }} سنة</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">الفصول الدراسية</p>
                                <p class="font-medium">{{ $profile->grades }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">رقم الهاتف</p>
                                <p class="font-medium">{{ $profile->phone ?? 'غير مضاف' }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">مكان السكن</p>
                                <p class="font-medium">{{ $profile->residence_place ?? 'غير مضاف' }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">مكان الإقامة</p>
                                <p class="font-medium">{{ $profile->current_location ?? 'غير مضاف' }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">المؤهل العلمي</p>
                                <p class="font-medium">{{ $profile->qualification ?? 'غير مضاف' }}</p>
                            </div>

                        </div>

                        @if ($profile->bio)
                            <div class="mt-6 border-t pt-4">
                                <p class="text-sm text-gray-500 mb-1">نبذة تعريفية</p>
                                <p class="text-gray-800 leading-relaxed">
                                    {{ $profile->bio }}
                                </p>
                            </div>
                        @endif

                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>