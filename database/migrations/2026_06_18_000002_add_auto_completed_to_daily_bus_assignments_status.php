<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL requires re-declaring the full enum to add a value
        DB::statement("ALTER TABLE daily_bus_assignments MODIFY status ENUM('active','completed','cancelled','auto_completed') NOT NULL DEFAULT 'active'");
    }

    public function down(): void
    {
        // Move any auto_completed rows back to completed before shrinking enum
        DB::statement("UPDATE daily_bus_assignments SET status = 'completed' WHERE status = 'auto_completed'");
        DB::statement("ALTER TABLE daily_bus_assignments MODIFY status ENUM('active','completed','cancelled') NOT NULL DEFAULT 'active'");
    }
};
