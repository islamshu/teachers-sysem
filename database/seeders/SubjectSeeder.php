<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            'اللغة العربية',
            'اللغة الإنجليزية',
            'الرياضيات',
            'العلوم',
            'الفيزياء',
            'الكيمياء',
            'الأحياء',
            'التاريخ',
            'الجغرافيا',
            'التربية الإسلامية',
            'التكنولوجيا',
        ];

        foreach ($subjects as $subject) {
            Subject::firstOrCreate([
                'name' => $subject,
            ]);
        }
    }
}