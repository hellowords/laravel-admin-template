<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use App\Events\UserLogin;
use App\Events\UserRegistered;
use App\Listeners\UpdateUserModel;
use App\Listeners\SendRegisteredEmailVerificatonNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
//        Registered::class => [
//            SendEmailVerificationNotification::class,
//        ],
        UserRegistered::class => [
            SendRegisteredEmailVerificatonNotification::class,
        ],
        UserLogin::class =>[
            UpdateUserModel::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
