<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherProfile extends Model
{
    protected $fillable = [
        'user_id',
        'photo',
        'phone',
        'birth_date',
        'gender',
        'subject_id',
        'experience_years',
        'residence_place',
        'current_location',
        'qualification',
        'practical_experience',
        'bio',
        'status',
        'employment_status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function grades()
    {
        return $this->belongsToMany(Grade::class);
    }

    public function employments()
    {
        return $this->hasMany(Employment::class, 'teacher_id');
    }

    public function activeEmployment()
    {
        return $this->hasOne(Employment::class, 'teacher_id')->where('status', 'hired');
    }
}