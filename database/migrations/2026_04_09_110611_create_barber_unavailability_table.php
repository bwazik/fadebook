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
        Schema::create('barber_unavailability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barber_id');
            $table->date('unavailable_date');
            $table->timestamps();

            $table->foreign('barber_id')->references('id')->on('barbers')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->unique(['barber_id', 'unavailable_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barber_unavailability');
    }
};
