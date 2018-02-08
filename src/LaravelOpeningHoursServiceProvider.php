<?php

namespace Webfactor\Laravel\OpeningHours;

use Illuminate\Support\ServiceProvider;

class LaravelOpeningHoursServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            if (!class_exists('CreateOpeningHoursTables')) {
                $timestamp = date('Y_m_d_His', time());
                $this->publishes([__DIR__.'/../database/migrations/create_opening_hours_tables.php' => database_path('migrations/'.$timestamp.'_create_opening_hours_tables.php')], 'migrations');
            }
            $this->publishes([__DIR__.'/../config/openinghours.php' => config_path('webfactor/openinghours.php')], 'config');
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}