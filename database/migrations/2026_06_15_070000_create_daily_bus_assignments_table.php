<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_bus_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_id')->constrained()->cascadeOnDelete();
            $table->foreignId('driver_id')->constrained('users')->cascadeOnDelete();
            $table->date('assigned_date');
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            // One driver per day; one bus per day
            $table->unique(['driver_id', 'assigned_date']);
            $table->unique(['bus_id', 'assigned_date']);
            $table->index('assigned_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_bus_assignments');
    }
};
