<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE buses MODIFY COLUMN status ENUM(
            'active',
            'on_trip',
            'arrived',
            'maintenance_due',
            'under_preventive_maintenance',
            'breakdown_reported',
            'under_repair',
            'awaiting_spare_parts',
            'ready_for_service',
            'out_of_service',
            'inactive'
        ) NOT NULL DEFAULT 'active'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE buses MODIFY COLUMN status ENUM(
            'active',
            'on_trip',
            'maintenance_due',
            'under_preventive_maintenance',
            'breakdown_reported',
            'under_repair',
            'awaiting_spare_parts',
            'ready_for_service',
            'out_of_service',
            'inactive'
        ) NOT NULL DEFAULT 'active'");
    }
};
