<?php

namespace App\Providers;

use App\Events\UpdatePasswordEvent;
use App\Listeners\changePasswordListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        UpdatePasswordEvent::class => [
            changePasswordListener::class,
        ],
    ];
}
