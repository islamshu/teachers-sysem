<?php

namespace App\Events;

use App\Models\Employment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmploymentUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $action,
        public Employment $employment
    ) {}

    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('school.' . $this->employment->school_id),
            new PrivateChannel('teacher.' . $this->employment->teacher->user_id),
        ];

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'employment.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'action' => $this->action,
            'employment_id' => $this->employment->id,
            'status' => $this->employment->status,
            'teacher_id' => $this->employment->teacher_id,
            'school_id' => $this->employment->school_id,
        ];
    }
}
