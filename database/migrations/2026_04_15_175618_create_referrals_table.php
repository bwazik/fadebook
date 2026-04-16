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
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('referrer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('invitee_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->onDelete('set null');
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->onDelete('set null');
            $table->tinyInteger('status')->default(0); // App\Enums\ReferralStatus::Pending
            $table->timestamp('rewarded_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('referrer_id');
            $table->index('invitee_id');
            $table->unique(['referrer_id', 'invitee_id']); // one record per referrer-invitee pair
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
