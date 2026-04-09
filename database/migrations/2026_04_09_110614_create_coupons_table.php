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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('shop_id');
            $table->string('code')->unique();
            $table->tinyInteger('discount_type')->default(1)->comment('1 => percentage, 2 => fixed');
            $table->decimal('discount_value', 8, 2);
            $table->dateTime('start_date')->nullable()->index();
            $table->dateTime('end_date')->nullable()->index();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('usage_limit')->nullable()->comment('Total uses allowed');
            $table->unsignedInteger('used_count')->default(0);
            $table->unsignedInteger('usage_limit_per_user')->nullable()->comment('Max uses per user');
            $table->decimal('minimum_amount', 8, 2)->nullable()->comment('Minimum booking amount to apply');
            $table->json('apply_to')->nullable()->comment('Categories or items coupon applies to');
            $table->json('except')->nullable()->comment('Categories or items coupon excludes');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('shop_id')->references('id')->on('shops')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->index(['shop_id', 'is_active']);
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
