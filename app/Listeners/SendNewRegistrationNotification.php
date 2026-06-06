<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\NewRegistration;
use Illuminate\Auth\Events\Registered;

class SendNewRegistrationNotification
{
    public function handle(Registered $event): void
    {
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewRegistration($event->user));
        }
    }
}
