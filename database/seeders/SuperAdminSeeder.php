<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use App\Support\EgyptianPhoneNumber;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $phone = EgyptianPhoneNumber::normalize((string) config('app.super_admin.phone'));

        if ($phone === null) {
            throw new \InvalidArgumentException('SUPER_ADMIN_PHONE must be a valid Egyptian number.');
        }

        User::query()->updateOrCreate(
            ['phone' => $phone],
            [
                'name' => config('app.super_admin.name'),
                'password' => Hash::make((string) config('app.super_admin.password')),
                'role' => UserRole::SuperAdmin,
                'status' => true,
                'no_show_strike_count' => 0,
            ],
        );
    }
}
