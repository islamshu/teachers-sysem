<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class GeneralTask extends Model
{
    protected $fillable = ['name', 'description', 'start_at', 'end_at', 'is_active', 'created_by'];

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'general_task_user');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'general_task_role');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logs()
    {
        return $this->hasMany(GeneralTaskLog::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now());
    }
}
