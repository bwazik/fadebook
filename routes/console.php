<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:update-booking-statuses')->everyFiveMinutes();
Schedule::command('phone-verifications:cleanup')->daily();
