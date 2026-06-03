<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            إكمال بيانات المدرس
        </h2>
    </x-slot>

    <div class="py-12" dir="rtl">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <form method="POST"
                          action="{{ route('teacher-profile.store') }}"
                          enctype="multipart/form-data">

                        @csrf

                        @include('frontend.teacher-profile.partials.form', [
                            'profile' => null,
                            'buttonText' => 'حفظ البيانات'
                        ])

                    </form>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>