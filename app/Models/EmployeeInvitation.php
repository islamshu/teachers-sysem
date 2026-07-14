<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeInvitation extends Model
{
    protected $fillable = [
        'school_id',
        'employee_id',
        'message',
        'status',
        'hired_at',
    ];

    protected function casts(): array
    {
        return [
            'hired_at' => 'datetime',
        ];
    }

    public function school()
    {
        return $this->belongsTo(User::class, 'school_id');
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function interview()
    {
        return $this->hasOne(EmployeeInterview::class);
    }
}
