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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('type')->index();
            $table->string('title');
            $table->text('description');
            $table->foreignId('coupon_id')->nullable()->constrained()->onDelete('set null');

            $table->timestamp('start_date')->nullable()->index();
            $table->timestamp('end_date')->nullable()->index();
            $table->boolean('is_active')->default(true)->index();

            $table->timestamps();
            $table->softDeletes();

            // Indexes for common discovery queries
            $table->index(['shop_id', 'is_active']);
            $table->index(['type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
