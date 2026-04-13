<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('session:prune')->dailyAt('23:59'); // session
Schedule::command('model:prune')->dailyAt('23:59'); // delete unused data
Schedule::command('app:reset-leave-quota') // reset leave quota every year
    ->yearlyOn(1, 1, '00:00')
    ->timezone('Asia/Jakarta');
Schedule::command('app:update-attendance-status') // update attendance status every day
    ->weekdays()
    ->dailyAt('23:59')
    ->timezone('Asia/Jakarta');

// jalankan: php artisan schedule:work
