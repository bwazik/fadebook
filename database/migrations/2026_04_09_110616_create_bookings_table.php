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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('booking_code', 6)->unique()->comment('Short code like #AB12CD');
            $table->foreignId('shop_id');
            $table->foreignId('client_id');
            $table->foreignId('barber_id')->nullable();
            $table->foreignId('service_id');
            $table->foreignId('coupon_id')->nullable();
            $table->dateTime('scheduled_at');
            $table->tinyInteger('status')->default(0)->comment('0 => pending, 1 => confirmed, 2 => in_progress, 3 => completed, 4 => cancelled, 5 => no_show');
            $table->decimal('service_price', 8, 2);
            $table->decimal('discount_amount', 8, 2)->default(0);
            $table->decimal('paid_amount', 8, 2)->default(0);
            $table->decimal('final_amount', 8, 2);
            $table->text('notes')->nullable();
            $table->boolean('policy_accepted')->default(false);
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('arrived_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->tinyInteger('cancelled_by')->nullable()->comment('1 => client, 2 => shop');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('shop_id')->references('id')->on('shops')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('client_id')->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('barber_id')->references('id')->on('barbers')
                ->onDelete('set null')->onUpdate('cascade');
            $table->foreign('service_id')->references('id')->on('services')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('coupon_id')->references('id')->on('coupons')
                ->onDelete('set null')->onUpdate('cascade');

            $table->index(['shop_id', 'status', 'scheduled_at']);
            $table->index(['client_id', 'status']);
            $table->index('booking_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
