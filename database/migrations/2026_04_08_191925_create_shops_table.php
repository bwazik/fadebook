<?php

use App\Enums\ShopStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users');
            $table->foreignId('area_id')->constrained();
            $table->string('name');
            $table->text('address');
            $table->string('phone', 11);
            $table->string('logo_path')->nullable();
            $table->tinyInteger('status')->default(ShopStatus::Pending->value);
            $table->string('rejection_reason')->nullable();
            $table->json('basic_services')->nullable();
            $table->unsignedInteger('barbers_count');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
