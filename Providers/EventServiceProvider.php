<?php

namespace GeekCms\Pages\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * TODO: Check why this provider dosen't register.
     *
     * @var array
     */
    protected $listen = [
        'GeekCms\PackagesManager\Events\ModulesEvent' => [
            'GeekCms\Pages\Listeners\ModulesListener',
        ],
    ];
}
