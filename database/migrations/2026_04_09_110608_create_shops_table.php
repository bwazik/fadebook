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
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('owner_id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('phone', 20);
            $table->text('address');
            $table->foreignId('area_id')->comment('Required for URL structure: domain.com/{area}/{shop}');
            $table->json('opening_hours')->comment('{"monday": {"open": "09:00", "close": "21:00"}, ...}');
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->unsignedInteger('total_reviews')->default(0);
            $table->unsignedInteger('total_views')->default(0);
            $table->unsignedInteger('total_bookings')->default(0);
            $table->tinyInteger('status')->default(0)->comment('0 => pending, 1 => active, 2 => suspended, 3 => rejected');
            $table->boolean('is_online')->default(true)->comment('Owner can toggle shop offline temporarily');
            $table->unsignedInteger('advance_booking_days')->default(7);
            $table->tinyInteger('barber_selection_mode')->default(1)->comment('1 => any_available, 2 => client_picks');
            $table->tinyInteger('payment_mode')->default(0)->comment('0 => no_payment, 1 => partial_deposit, 2 => full_payment');
            $table->decimal('deposit_percentage', 5, 2)->nullable()->comment('If partial deposit mode');
            $table->decimal('commission_rate', 5, 2)->default(10.00)->comment('Platform commission % set by admin');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('owner_id')->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('area_id')->references('id')->on('areas')
                ->onDelete('restrict')->onUpdate('cascade');

            $table->index(['status', 'is_online']);
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
