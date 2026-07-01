<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractAttachment extends Model
{
    protected $fillable = [
        'name',
        'is_enabled',
        'is_required',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'is_required' => 'boolean',
        ];
    }
}
