<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeInterview extends Model
{
    protected $fillable = [
        'employee_invitation_id',
        'interviewer_id',
        'total_score',
        'max_score',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'total_score' => 'decimal:2',
            'max_score' => 'decimal:2',
        ];
    }

    public function invitation()
    {
        return $this->belongsTo(EmployeeInvitation::class, 'employee_invitation_id');
    }

    public function interviewer()
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }

    public function answers()
    {
        return $this->hasMany(EmployeeInterviewAnswer::class, 'employee_interview_id');
    }
}
