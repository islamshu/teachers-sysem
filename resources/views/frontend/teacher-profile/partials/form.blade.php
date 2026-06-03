@php
    $selectedGrades = old(
        'grade_ids',
        $profile ? $profile->grades->pluck('id')->toArray() : []
    );
@endphp

@if ($profile && $profile->photo)
    <div class="mb-6">
        <p class="mb-2 text-sm text-gray-600">الصورة الحالية</p>
        <img src="{{ asset('storage/' . $profile->photo) }}"
             class="w-28 h-28 rounded-full object-cover border">
    </div>
@endif

<div class="mb-4">
    <label class="block mb-1 font-medium text-gray-700">الصورة الشخصية</label>
    <input type="file" name="photo" class="w-full border rounded-lg p-2">
    @error('photo')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    <div>
        <label class="block mb-1 font-medium text-gray-700">رقم الهاتف</label>
        <input type="text"
               name="phone"
               value="{{ old('phone', $profile->phone ?? '') }}"
               class="w-full border rounded-lg p-2">
        @error('phone')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block mb-1 font-medium text-gray-700">تاريخ الميلاد</label>
        <input type="date"
               name="birth_date"
               value="{{ old('birth_date', $profile->birth_date ?? '') }}"
               class="w-full border rounded-lg p-2">
        @error('birth_date')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block mb-1 font-medium text-gray-700">الجنس</label>
        <select name="gender" class="w-full border rounded-lg p-2">
            <option value="">اختر الجنس</option>
            <option value="male" @selected(old('gender', $profile->gender ?? '') === 'male')>ذكر</option>
            <option value="female" @selected(old('gender', $profile->gender ?? '') === 'female')>أنثى</option>
        </select>
        @error('gender')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block mb-1 font-medium text-gray-700">المبحث</label>
        <select name="subject_id" class="w-full border rounded-lg p-2">
            <option value="">اختر المبحث</option>

            @foreach ($subjects as $subject)
                <option value="{{ $subject->id }}"
                    @selected(old('subject_id', $profile->subject_id ?? '') == $subject->id)>
                    {{ $subject->name }}
                </option>
            @endforeach
        </select>

        @error('subject_id')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block mb-1 font-medium text-gray-700">عدد سنوات الخبرة</label>
        <input type="number"
               name="experience_years"
               min="0"
               value="{{ old('experience_years', $profile->experience_years ?? 0) }}"
               class="w-full border rounded-lg p-2">
        @error('experience_years')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block mb-1 font-medium text-gray-700">الفصول الدراسية</label>

        <select name="grade_ids[]"
                multiple
                class="w-full border rounded-lg p-2 h-32">

            @foreach ($grades as $grade)
                <option value="{{ $grade->id }}"
                    @selected(in_array($grade->id, $selectedGrades))>
                    {{ $grade->name }}
                </option>
            @endforeach
        </select>

        <p class="text-xs text-gray-500 mt-1">
            يمكنك اختيار أكثر من صف بالضغط على Ctrl.
        </p>

        @error('grade_ids')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror

        @error('grade_ids.*')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block mb-1 font-medium text-gray-700">مكان السكن</label>
        <input type="text"
               name="residence_place"
               value="{{ old('residence_place', $profile->residence_place ?? '') }}"
               class="w-full border rounded-lg p-2">
        @error('residence_place')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block mb-1 font-medium text-gray-700">مكان الإقامة الحالي</label>
        <input type="text"
               name="current_location"
               value="{{ old('current_location', $profile->current_location ?? '') }}"
               class="w-full border rounded-lg p-2">
        @error('current_location')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

</div>

<div class="mt-4">
    <label class="block mb-1 font-medium text-gray-700">المؤهل العلمي</label>
    <input type="text"
           name="qualification"
           value="{{ old('qualification', $profile->qualification ?? '') }}"
           class="w-full border rounded-lg p-2">
    @error('qualification')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mt-4">
    <label class="block mb-1 font-medium text-gray-700">نبذة تعريفية</label>
    <textarea name="bio"
              rows="5"
              class="w-full border rounded-lg p-2">{{ old('bio', $profile->bio ?? '') }}</textarea>
    @error('bio')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mt-6 flex items-center gap-3">
    <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
        {{ $buttonText }}
    </button>

    <a href="{{ route('dashboard') }}"
       class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded-lg">
        رجوع
    </a>
</div>