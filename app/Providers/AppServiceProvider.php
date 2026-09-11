<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Paginator::useTailwind();

        Event::listen(Login::class, function (Login $event) {
            /** @var \App\Models\User $user */
            $user = $event->user;
            $user->updateQuietly(['last_login_at' => now()]);
        });
    }
}
