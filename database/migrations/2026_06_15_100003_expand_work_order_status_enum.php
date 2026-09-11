<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE work_orders MODIFY COLUMN status ENUM(
            'reported','pending','assessment','pending_approval',
            'assigned','awaiting_parts','in_progress','testing',
            'completed','returned_to_service','cancelled'
        ) NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE work_orders MODIFY COLUMN status ENUM(
            'pending','assigned','in_progress','completed','cancelled'
        ) NOT NULL DEFAULT 'pending'");
    }
};
