<?php

namespace App\Listeners;

use App\Events\UpdatePasswordEvent;

class changePasswordListener
{
    /**
     * Handle the event.
     */
    public function handle(UpdatePasswordEvent $event): void
    {
        $event->user->last_update_password = now();
        $event->user->save();
    }
}
