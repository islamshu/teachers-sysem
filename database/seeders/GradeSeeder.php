<?php

namespace Database\Seeders;

use App\Models\Grade;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    public function run(): void
    {
        $grades = [
            'الصف الأول',
            'الصف الثاني',
            'الصف الثالث',
            'الصف الرابع',
            'الصف الخامس',
            'الصف السادس',
            'الصف السابع',
            'الصف الثامن',
            'الصف التاسع',
            'الصف العاشر',
            'الصف الحادي عشر',
            'التوجيهي',
        ];

        foreach ($grades as $grade) {
            Grade::firstOrCreate([
                'name' => $grade,
            ]);
        }
    }
}