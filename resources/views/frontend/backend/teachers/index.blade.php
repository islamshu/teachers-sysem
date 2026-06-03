@extends('backend.layouts.app')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">
        إدارة المدرسين
    </h1>

    <p class="text-gray-600 mt-1">
        من هنا يمكنك مراجعة بيانات المدرسين وقبولهم أو رفضهم.
    </p>
</div>

@if (session('success'))
    <div class="mb-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white shadow-sm rounded-lg overflow-hidden">

    <table class="w-full text-sm text-right">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="p-4">المدرس</th>
                <th class="p-4">المبحث</th>
                <th class="p-4">الصفوف</th>
                <th class="p-4">الخبرة</th>
                <th class="p-4">الهاتف</th>
                <th class="p-4">الحالة</th>
                <th class="p-4">الإجراءات</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($teachers as $teacher)
                <tr class="border-b hover:bg-gray-50">

                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            @if ($teacher->photo)
                                <img src="{{ asset('storage/' . $teacher->photo) }}"
                                     class="w-12 h-12 rounded-full object-cover border">
                            @else
                                <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-xs text-gray-500">
                                    صورة
                                </div>
                            @endif

                            <div>
                                <p class="font-semibold text-gray-900">
                                    {{ $teacher->user->name }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ $teacher->user->email }}
                                </p>
                            </div>
                        </div>
                    </td>

                    <td class="p-4">
                        {{ $teacher->subject->name ?? '-' }}
                    </td>

                    <td class="p-4">
                        <div class="flex flex-wrap gap-1">
                            @foreach ($teacher->grades as $grade)
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">
                                    {{ $grade->name }}
                                </span>
                            @endforeach
                        </div>
                    </td>

                    <td class="p-4">
                        {{ $teacher->experience_years }} سنة
                    </td>

                    <td class="p-4">
                        {{ $teacher->phone ?? '-' }}
                    </td>

                    <td class="p-4">
                        @if ($teacher->status === 'approved')
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs">
                                مقبول
                            </span>
                        @elseif ($teacher->status === 'rejected')
                            <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs">
                                مرفوض
                            </span>
                        @else
                            <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs">
                                قيد المراجعة
                            </span>
                        @endif
                    </td>

                    <td class="p-4">
                        <div class="flex items-center gap-2">

                            <form method="POST"
                                  action="{{ route('admin.teachers.approve', $teacher) }}">
                                @csrf
                                @method('PATCH')

                                <button class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs">
                                    قبول
                                </button>
                            </form>

                            <form method="POST"
                                  action="{{ route('admin.teachers.reject', $teacher) }}">
                                @csrf
                                @method('PATCH')

                                <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs">
                                    رفض
                                </button>
                            </form>

                        </div>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="7" class="p-6 text-center text-gray-500">
                        لا يوجد مدرسين حالياً.
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