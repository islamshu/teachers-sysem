<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralTaskLog extends Model
{
    protected $fillable = ['general_task_id', 'user_id', 'completed_at', 'notes', 'completion_attachment'];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
        ];
    }

    public function generalTask()
    {
        return $this->belongsTo(GeneralTask::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
