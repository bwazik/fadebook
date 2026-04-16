<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:send-booking-reminders')->everyFiveMinutes();

Schedule::command('app:update-booking-statuses')->everyFiveMinutes();
Schedule::command('banhafade:send-review-requests')->everyTenMinutes();
Schedule::command('phone-verifications:cleanup')->daily();
