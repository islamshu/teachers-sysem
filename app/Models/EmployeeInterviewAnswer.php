<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeInterviewAnswer extends Model
{
    protected $fillable = [
        'employee_interview_id',
        'interview_question_id',
        'score',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
        ];
    }

    public function interview()
    {
        return $this->belongsTo(EmployeeInterview::class, 'employee_interview_id');
    }

    public function question()
    {
        return $this->belongsTo(InterviewQuestion::class, 'interview_question_id');
    }
}
