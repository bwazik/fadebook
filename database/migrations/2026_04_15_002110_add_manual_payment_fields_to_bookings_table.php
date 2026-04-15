<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('payment_method_id')->nullable()->after('coupon_id')->constrained('shop_payment_methods');
            $table->string('payment_reference')->nullable()->after('payment_method_id');
            $table->decimal('deposit_amount', 10, 2)->default(0)->after('final_amount');
            $table->decimal('commission_amount', 10, 2)->default(0)->after('deposit_amount');
            $table->timestamp('payment_verified_at')->nullable()->after('payment_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_method_id');
            $table->dropColumn([
                'payment_reference',
                'deposit_amount',
                'commission_amount',
                'payment_verified_at',
            ]);
        });
    }
};
