<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('audit:backfill', function () {
    $this->call(\App\Console\Commands\BackfillAuditLogs::class);
})->describe('Backfill audit log from existing data');