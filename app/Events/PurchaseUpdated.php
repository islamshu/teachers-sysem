<?php

namespace App\Events;

use App\Models\Purchase;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PurchaseUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $action,
        public Purchase $purchase
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('purchases'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'purchase.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'action' => $this->action,
            'purchase_id' => $this->purchase->id,
        ];
    }
}
