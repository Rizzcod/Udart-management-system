<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mileage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_id')->constrained()->cascadeOnDelete();
            $table->foreignId('driver_id')->constrained('users')->cascadeOnDelete();
            $table->date('trip_date');
            $table->unsignedInteger('km_traveled');
            $table->unsignedInteger('odometer_reading');
            $table->string('route', 255)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['bus_id', 'trip_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mileage_logs');
    }
};
