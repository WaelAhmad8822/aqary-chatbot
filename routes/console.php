<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('migrate {--force}', function () {
    $this->info('Migrate command successfully bypassed in microservice.');
})->purpose('Dummy migrate command for Laravel Cloud.');
