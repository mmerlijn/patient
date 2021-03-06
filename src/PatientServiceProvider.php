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
            __DIR__ . '/../config/patient.php', 'patient'
        );
    }

    public function boot()
    {
        Requester::observe(RequesterObserver::class);
        Patient::observe(PatientObserver::class);

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}