<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyTaskLog extends Model
{
    protected $fillable = ['fixed_task_id', 'user_id', 'date', 'completed_at', 'notes'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    public function fixedTask()
    {
        return $this->belongsTo(FixedTask::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
