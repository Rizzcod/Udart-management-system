<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Extend the status enum to include the system-managed 'out_of_service' value
        DB::statement("ALTER TABLE buses MODIFY COLUMN status ENUM('active','inactive','under_repair','out_of_service') NOT NULL DEFAULT 'active'");

        Schema::table('buses', function (Blueprint $table) {
            // True when the PM system has automatically restricted this bus
            $table->boolean('maintenance_locked')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('buses', function (Blueprint $table) {
            $table->dropColumn('maintenance_locked');
        });

        DB::statement("ALTER TABLE buses MODIFY COLUMN status ENUM('active','inactive','under_repair') NOT NULL DEFAULT 'active'");
    }
};
