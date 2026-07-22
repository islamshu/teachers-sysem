<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralTaskAttachment extends Model
{
    protected $fillable = ['general_task_id', 'name', 'file_path'];

    public function generalTask()
    {
        return $this->belongsTo(GeneralTask::class);
    }
}
