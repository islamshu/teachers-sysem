<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralTaskCompletion extends Model
{
    protected $fillable = ['general_task_id', 'user_id', 'general_task_attachment_id', 'file_path'];

    public function generalTask()
    {
        return $this->belongsTo(GeneralTask::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attachment()
    {
        return $this->belongsTo(GeneralTaskAttachment::class);
    }
}
