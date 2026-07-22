<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class GeneralTask extends Model
{
    protected $fillable = ['name', 'description', 'start_at', 'end_at', 'is_active', 'created_by', 'attachment_required'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'attachment_required' => 'boolean',
        ];
    }

    protected function startAt(): Attribute
    {
        return Attribute::get(fn ($value) => \Carbon\Carbon::parse($value)->format('Y-m-d\TH:i:s'));
    }

    protected function endAt(): Attribute
    {
        return Attribute::get(fn ($value) => \Carbon\Carbon::parse($value)->format('Y-m-d\TH:i:s'));
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

    public function attachments()
    {
        return $this->hasMany(GeneralTaskAttachment::class);
    }

    public function completions()
    {
        return $this->hasMany(GeneralTaskCompletion::class);
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
