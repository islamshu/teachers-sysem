<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function employees()
    {
        return $this->belongsToMany(User::class)->where('role', 'employee')->withTimestamps();
    }
}
