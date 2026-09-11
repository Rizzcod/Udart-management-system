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
        Schema::table('failure_predictions', function (Blueprint $table) {
            $table->json('feature_contributions')->nullable()->after('recommended_action');
        });
    }

    public function down(): void
    {
        Schema::table('failure_predictions', function (Blueprint $table) {
            $table->dropColumn('feature_contributions');
        });
    }
};
