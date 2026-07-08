<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterviewQuestion extends Model
{
    protected $fillable = [
        'question',
        'category',
        'sort_order',
    ];

    public function answers()
    {
        return $this->hasMany(InterviewAnswer::class);
    }
}
