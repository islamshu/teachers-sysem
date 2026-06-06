<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolProfile extends Model
{
    protected $fillable = [
        'user_id',
        'school_name',
        'commercial_register',
        'tax_id',
        'license_number',
        'phone',
        'address',
        'website',
        'principal_name',
        'logo',
        'bio',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
