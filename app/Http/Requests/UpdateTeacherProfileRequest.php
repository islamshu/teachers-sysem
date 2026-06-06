<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeacherProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp'],
            'phone' => ['required', 'string', 'max:30'],
            'birth_date' => ['required', 'date'],
            'gender' => ['required', 'in:male,female'],

            'subject_id' => ['required', 'exists:subjects,id'],
            'grade_ids' => ['required', 'array', 'min:1'],
            'grade_ids.*' => ['exists:grades,id'],

            'experience_years' => ['required', 'integer', 'min:0', 'max:60'],
            'residence_place' => ['required', 'string', 'max:255'],
            'current_location' => ['required', 'string', 'max:255'],
            'qualification' => ['required', 'string', 'max:255'],
            'practical_experience' => ['required', 'string'],
            'bio' => ['required', 'string'],
        ];
    }
}