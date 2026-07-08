<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'item_name',
        'amount',
        'notes',
        'requires_invoice',
        'created_by',
        'status',
        'actual_amount',
        'remaining_amount',
        'invoice_image',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'requires_invoice' => 'boolean',
            'actual_amount' => 'decimal:2',
            'remaining_amount' => 'decimal:2',
            'completed_at' => 'datetime',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function employees()
    {
        return $this->belongsToMany(User::class, 'purchase_user')->withTimestamps();
    }
}
