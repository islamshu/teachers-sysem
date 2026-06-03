<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    private array $firstNames = [
        'أحمد', 'محمد', 'علي', 'عمر', 'خالد', 'محمود', 'حسن', 'حسين', 'إبراهيم', 'ياسر',
        'نور', 'سارة', 'مريم', 'فاطمة', 'هدى', 'آمنة', 'رنا', 'ليلى', 'دينا', 'سامية',
    ];

    private array $lastNames = [
        'السيد', 'الشافعي', 'النجار', 'موسى', 'عبد الله', 'خليل', 'شعبان', 'عبد الرحمن',
        'الجمل', 'عمار', 'صابر', 'قاسم', 'زكي', 'العبد', 'سلامة', 'ناصر', 'الأسمر', 'الشيخ',
    ];

    private array $residencePlaces = [
        'غزة', 'رفح', 'خان يونس', 'جباليا', 'بيت لاهيا', 'بيت حانون', 'دير البلح', 'النصيرات',
        'المغازي', 'بريج', 'الشجاعية', 'الرمال', 'النصر', 'التفاح', 'الصبرة', 'تل الهوى',
    ];

    private array $qualifications = [
        'بكالوريوس', 'ماجستير', 'دكتوراه', 'دبلوم عالي',
    ];

    private array $specializations = [
        'اللغة العربية', 'الرياضيات', 'العلوم', 'اللغة الإنجليزية', 'التربية الإسلامية',
        'التاريخ', 'الجغرافيا', 'الفيزياء', 'الكيمياء', 'الأحياء',
    ];

    public function run(): void
    {
        $subjects = Subject::pluck('id')->toArray();
        $grades = Grade::pluck('id')->toArray();

        if (empty($subjects) || empty($grades)) {
            $this->command->error('Please run SubjectSeeder and GradeSeeder first.');
            return;
        }

        $statuses = ['approved', 'pending', 'rejected'];
        $genders = ['male', 'female'];
        $userIds = [];

        for ($i = 0; $i < 50; $i++) {
            $firstName = $this->firstNames[array_rand($this->firstNames)];
            $lastName = $this->lastNames[array_rand($this->lastNames)];
            $gender = $genders[array_rand($genders)];
            $status = $statuses[array_rand($statuses)];

            $user = User::create([
                'name' => "$firstName $lastName",
                'email' => "teacher{$i}_" . uniqid() . "@example.com",
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);

            $userIds[] = $user->id;

            $birthYear = rand(1980, 2002);
            $birthMonth = str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT);
            $birthDay = str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);

            $profile = TeacherProfile::create([
                'user_id' => $user->id,
                'phone' => '05' . rand(10000000, 99999999),
                'birth_date' => "{$birthYear}-{$birthMonth}-{$birthDay}",
                'gender' => $gender,
                'subject_id' => $subjects[array_rand($subjects)],
                'experience_years' => rand(1, 25),
                'residence_place' => $this->residencePlaces[array_rand($this->residencePlaces)],
                'current_location' => $this->residencePlaces[array_rand($this->residencePlaces)],
                'qualification' => $this->qualifications[array_rand($this->qualifications)],
                'bio' => "مدرس {$this->specializations[array_rand($this->specializations)]} بخبرة في تدريس المناهج الفلسطينية. أؤمن بأهمية التعليم وأسعى لتطوير مهارات الطلاب.",
                'status' => $status,
            ]);

            $randomGrades = (array)array_rand(array_flip($grades), rand(2, 4));
            $profile->grades()->attach($randomGrades);
        }
    }
}
