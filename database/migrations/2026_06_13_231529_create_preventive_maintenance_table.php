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
        Schema::create('preventive_maintenance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_id')->constrained()->cascadeOnDelete();
            $table->string('service_type');
            $table->unsignedInteger('interval_days')->nullable();
            $table->unsignedInteger('interval_km')->nullable();
            $table->date('last_service_date')->nullable();
            $table->unsignedInteger('last_service_km')->nullable();
            $table->date('next_service_date')->nullable();
            $table->enum('status', ['upcoming', 'overdue', 'completed'])->default('upcoming');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preventive_maintenance');
    }
};
