<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    protected $fillable = [
        'employment_id',
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

    public function employment()
    {
        return $this->belongsTo(Employment::class);
    }

    public function interviewer()
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }

    public function answers()
    {
        return $this->hasMany(InterviewAnswer::class);
    }
}
