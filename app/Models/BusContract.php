<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusContract extends Model
{
    protected $fillable = [
        'owner_name',
        'same_driver',
        'owner_phone',
        'driver_name',
        'driver_phone',
        'license',
        'van_color',
        'seats_count',
        'location',
    ];

    protected function casts(): array
    {
        return [
            'same_driver' => 'boolean',
            'seats_count' => 'integer',
        ];
    }

    public function attachmentFiles()
    {
        return $this->hasMany(BusContractAttachment::class);
    }

    public function attachments()
    {
        return $this->belongsToMany(ContractAttachment::class, 'bus_contract_attachment')
            ->withPivot('file_path')
            ->withTimestamps();
    }
}
