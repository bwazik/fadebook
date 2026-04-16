<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->boolean('referral_enabled')
                ->default(false)
                ->after('is_online')
                ->comment('Shop opts-in to the referral programme; shows on the Offers discovery page');

            $table->index('referral_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropIndex(['referral_enabled']);
            $table->dropColumn('referral_enabled');
        });
    }
};
