<?php

namespace Database\Seeders;

use App\Models\InterviewQuestion;
use Illuminate\Database\Seeder;

class InterviewQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [
            ['question' => 'ما هي خبراتك السابقة في التدريس؟', 'category' => 'خبرات', 'sort_order' => 1],
            ['question' => 'كيف تتعامل مع الطلاب ذوي المستويات المختلفة؟', 'category' => 'مهارات تدريسية', 'sort_order' => 2],
            ['question' => 'ما هي أساليبك في إدارة الفصل؟', 'category' => 'إدارة صفية', 'sort_order' => 3],
            ['question' => 'كيف تستخدم التكنولوجيا في التعليم؟', 'category' => 'مهارات تقنية', 'sort_order' => 4],
            ['question' => 'كيف تتعامل مع أولياء الأمور؟', 'category' => 'تواصل', 'sort_order' => 5],
            ['question' => 'ما هي خطتك لتطوير نفسك مهنياً؟', 'category' => 'تطوير ذاتي', 'sort_order' => 6],
            ['question' => 'كيف تتعامل مع الطلاب ذوي الاحتياجات الخاصة؟', 'category' => 'مهارات تدريسية', 'sort_order' => 7],
            ['question' => 'هل لديك خبرة في المناهج الدراسية الحديثة؟', 'category' => 'خبرات', 'sort_order' => 8],
            ['question' => 'ما هي نقاط قوتك كمعلم؟', 'category' => 'شخصية', 'sort_order' => 9],
            ['question' => 'كيف تقيّم أداء الطلاب؟', 'category' => 'تقييم', 'sort_order' => 10],
        ];

        foreach ($questions as $q) {
            InterviewQuestion::create($q);
        }
    }
}
