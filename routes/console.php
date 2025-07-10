<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('alerts:expire')->everyMinute();
Schedule::command('devices:update-rain')->everyMinute();
Schedule::command('network:check')->everyMinute();
Schedule::command('sync:pull-main')->everyFiveMinutes();
Schedule::command('sync:push-main')->everyFiveMinutes();
