<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

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
        'rejection_reason',
        'approved_at',
        'approved_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'requires_invoice' => 'boolean',
            'actual_amount' => 'decimal:2',
            'remaining_amount' => 'decimal:2',
            'completed_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function employees()
    {
        return $this->belongsToMany(User::class, 'purchase_user')->withTimestamps();
    }

    public function assignedEmployee()
    {
        return $this->belongsToMany(User::class, 'purchase_user')->withTimestamps()->first();
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }
}
