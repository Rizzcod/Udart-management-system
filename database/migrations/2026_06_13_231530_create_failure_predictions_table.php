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
        Schema::create('failure_predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_id')->constrained()->cascadeOnDelete();
            $table->enum('risk_level', ['low', 'medium', 'high']);
            $table->string('predicted_failure_type')->nullable();
            $table->decimal('confidence_score', 5, 2)->nullable();
            $table->text('recommended_action')->nullable();
            $table->timestamp('predicted_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('failure_predictions');
    }
};
