<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            تعديل بيانات المدرس
        </h2>
    </x-slot>

    <div class="py-12" dir="rtl">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <form method="POST"
                          action="{{ route('teacher-profile.update') }}"
                          enctype="multipart/form-data">

                        @csrf
                        @method('PUT')

                        @include('frontend.teacher-profile.partials.form', [
                            'profile' => $profile,
                            'buttonText' => 'تحديث البيانات'
                        ])

                    </form>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>