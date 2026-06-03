<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            المدرسون
        </h2>
    </x-slot>

    <div class="py-12" dir="rtl">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Filters -->
            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">

                <form method="GET" action="{{ route('teachers.index') }}">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                المادة
                            </label>

                            <select
                                name="subject_id"
                                class="w-full border rounded-lg p-2">

                                <option value="">
                                    جميع المواد
                                </option>

                                @foreach($subjects as $subject)
                                    <option
                                        value="{{ $subject->id }}"
                                        @selected(request('subject_id') == $subject->id)>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                الصف الدراسي
                            </label>

                            <select
                                name="grade_id"
                                class="w-full border rounded-lg p-2">

                                <option value="">
                                    جميع الصفوف
                                </option>

                                @foreach($grades as $grade)
                                    <option
                                        value="{{ $grade->id }}"
                                        @selected(request('grade_id') == $grade->id)>
                                        {{ $grade->name }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        <div class="flex items-end">

                            <button
                                type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg">

                                بحث

                            </button>

                        </div>

                    </div>

                </form>

            </div>

            <!-- Teachers -->

            @if($teachers->count())

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                    @foreach($teachers as $teacher)

                        <div class="bg-white rounded-xl shadow-sm overflow-hidden">

                            <div class="p-6">

                                <div class="flex items-center gap-4 mb-4">

                                    @if($teacher->photo)

                                        <img
                                            src="{{ asset('storage/' . $teacher->photo) }}"
                                            class="w-20 h-20 rounded-full object-cover border">

                                    @else

                                        <div class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                                            صورة
                                        </div>

                                    @endif

                                    <div>

                                        <h3 class="font-bold text-lg text-gray-900">
                                            {{ $teacher->user->name }}
                                        </h3>

                                        <p class="text-gray-600">
                                            {{ $teacher->subject->name ?? '-' }}
                                        </p>

                                    </div>

                                </div>

                                <div class="space-y-2 text-sm">

                                    <p>
                                        <strong>سنوات الخبرة:</strong>
                                        {{ $teacher->experience_years }}
                                    </p>

                                    <p>
                                        <strong>المؤهل:</strong>
                                        {{ $teacher->qualification ?? '-' }}
                                    </p>

                                    <p>
                                        <strong>مكان السكن:</strong>
                                        {{ $teacher->residence_place ?? '-' }}
                                    </p>

                                </div>

                                <div class="mt-4">

                                    <p class="font-medium mb-2">
                                        الصفوف:
                                    </p>

                                    <div class="flex flex-wrap gap-2">

                                        @foreach($teacher->grades as $grade)

                                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">

                                                {{ $grade->name }}

                                            </span>

                                        @endforeach

                                    </div>

                                </div>

                                @if($teacher->bio)

                                    <div class="mt-4 border-t pt-4">

                                        <p class="text-sm text-gray-600 line-clamp-3">

                                            {{ $teacher->bio }}

                                        </p>

                                    </div>

                                @endif

                            </div>

                        </div>

                    @endforeach

                </div>

                <div class="mt-8">
                    {{ $teachers->withQueryString()->links() }}
                </div>

            @else

                <div class="bg-white rounded-lg shadow-sm p-10 text-center">

                    <h3 class="text-lg font-semibold text-gray-800">
                        لا يوجد مدرسون مطابقون للبحث
                    </h3>

                </div>

            @endif

        </div>
    </div>
</x-app-layout>