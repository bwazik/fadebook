<?php

use App\Enums\UserRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 11)->unique()->after('name');
            $table->string('email')->nullable()->change();
            $table->tinyInteger('role')->default(UserRole::Client->value)->after('password');
            $table->boolean('status')->default(true)->after('role');
            $table->unsignedInteger('no_show_strike_count')->default(0)->after('status');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['phone', 'role', 'status', 'no_show_strike_count']);
            $table->string('email')->nullable(false)->change();
        });
    }
};
