<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $phone = env('SUPER_ADMIN_PHONE', '01000000001');
        $password = env('SUPER_ADMIN_PASSWORD', 'password');

        User::updateOrCreate(
            ['phone' => $phone],
            [
                'name' => 'مدير النظام',
                'password' => Hash::make($password),
                'role' => UserRole::SuperAdmin,
            ]
        );
    }
}
