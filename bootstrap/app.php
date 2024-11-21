<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use App\Models\User;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->withProviders([
        \Barryvdh\DomPDF\ServiceProvider::class,  
    ])->withSchedule(function (Schedule $schedule) {
        $schedule->call(function () {
            $user = User::all();

            foreach($user as $u){
                $u->update([
                    'verification_token'=> null,
                    'authenticated'=> 'false'
                ]);
            }

        })->timezone('Asia/Manila')->daily()->at('23:59');
    })
    ->create();
