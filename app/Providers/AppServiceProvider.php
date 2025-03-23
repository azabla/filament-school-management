<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
//AppServiceProvider.php
use Illuminate\Support\Facades\Gate;

use App\Models\Teacher;
use App\Policies\TeacherPolicy;


class AppServiceProvider extends ServiceProvider
{

    protected $policies = [
        
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    //    Gate::policy(\App\Models\Mark::class, \App\Policies\MarkPolicy::class);
        // Gate::policy(\Ramnzys\FilamentEmailLog\Models\Email::class, \App\Policies\EmailPolicy::class);
    }
}