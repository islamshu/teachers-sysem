@extends('backend.layouts.app')

@section('content')

<div class="flex items-center justify-between mb-6">

    <div>
        <h1 class="text-2xl font-bold text-gray-900">
            إدارة المدرسين
        </h1>

        <p class="text-gray-600 mt-1">
            مراجعة وقبول أو رفض المدرسين
        </p>
    </div>

</div>

@if(session('success'))
    <div class="mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-xl shadow overflow-hidden">

    <table class="min-w-full">

        <thead class="bg-gray-50 border-b">

            <tr>

                <th class="px-4 py-3 text-right">
                    المدرس
                </th>

                <th class="px-4 py-3 text-right">
                    المادة
                </th>

                <th class="px-4 py-3 text-right">
                    الصفوف
                </th>

                <th class="px-4 py-3 text-right">
                    الخبرة
                </th>

                <th class="px-4 py-3 text-right">
                    الهاتف
                </th>

                <th class="px-4 py-3 text-right">
                    الحالة
                </th>

                <th class="px-4 py-3 text-right">
                    العمليات
                </th>

            </tr>

        </thead>

        <tbody>

        @forelse($teachers as $teacher)

            <tr class="border-b">

                <td class="px-4 py-4">

                    <div class="flex items-center gap-3">

                        @if($teacher->photo)

                            <img
                                src="{{ asset('storage/' . $teacher->photo) }}"
                                class="w-12 h-12 rounded-full object-cover border">

                        @else

                            <div class="w-12 h-12 rounded-full bg-gray-200"></div>

                        @endif

                        <div>

                            <div class="font-semibold">
                                {{ $teacher->user->name }}
                            </div>

                            <div class="text-sm text-gray-500">
                                {{ $teacher->user->email }}
                            </div>

                        </div>

                    </div>

                </td>

                <td class="px-4 py-4">
                    {{ $teacher->subject->name ?? '-' }}
                </td>

                <td class="px-4 py-4">

                    <div class="flex flex-wrap gap-1">

                        @foreach($teacher->grades as $grade)

                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                                {{ $grade->name }}
                            </span>

                        @endforeach

                    </div>

                </td>

                <td class="px-4 py-4">
                    {{ $teacher->experience_years }}
                </td>

                <td class="px-4 py-4">
                    {{ $teacher->phone }}
                </td>

                <td class="px-4 py-4">

                    @if($teacher->status == 'approved')

                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                            مقبول
                        </span>

                    @elseif($teacher->status == 'rejected')

                        <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs">
                            مرفوض
                        </span>

                    @else

                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">
                            قيد المراجعة
                        </span>

                    @endif

                </td>

                <td class="px-4 py-4">

                    <div class="flex gap-2">

                        <form
                            action="{{ route('admin.teachers.approve', $teacher) }}"
                            method="POST">

                            @csrf
                            @method('PATCH')

                            <button
                                class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                                قبول
                            </button>

                        </form>

                        <form
                            action="{{ route('admin.teachers.reject', $teacher) }}"
                            method="POST">

                            @csrf
                            @method('PATCH')

                            <button
                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                                رفض
                            </button>

                        </form>

                    </div>

                </td>

            </tr>

        @empty

            <tr>

                <td colspan="7" class="text-center py-10 text-gray-500">
                    لا يوجد مدرسين
                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

</div>

<div class="mt-6">
    {{ $teachers->links() }}
</div>

@endsection