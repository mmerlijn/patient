<?php

namespace mmerlijn\patient;

use Illuminate\Support\ServiceProvider;
use mmerlijn\patient\Models\Patient;
use mmerlijn\patient\Models\Requester;
use mmerlijn\patient\Observers\PatientObserver;
use mmerlijn\patient\Observers\RequesterObserver;

class PatientServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/config.php', 'config'
        );
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        Requester::observe(RequesterObserver::class);
        Patient::observe(PatientObserver::class);
    }
}