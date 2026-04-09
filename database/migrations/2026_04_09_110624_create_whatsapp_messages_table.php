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
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->foreignId('shop_id')->nullable();
            $table->string('phone', 20);
            $table->string('template');
            $table->tinyInteger('queue_type')->default(1)->comment('1 => instant, 2 => urgent, 3 => default');
            $table->json('data');
            $table->tinyInteger('status')->default(1)->comment('1 => queued, 2 => sent, 3 => failed');
            $table->text('error_message')->nullable();
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('shop_id')->references('id')->on('shops')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->index(['status', 'queue_type']);
            $table->index('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};
