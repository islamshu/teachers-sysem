<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employment extends Model
{
    protected $fillable = [
        'school_id',
        'teacher_id',
        'subject_id',
        'message',
        'contact_phone',
        'contact_email',
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

    public function teacher()
    {
        return $this->belongsTo(TeacherProfile::class, 'teacher_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function interview()
    {
        return $this->hasOne(Interview::class);
    }
}
