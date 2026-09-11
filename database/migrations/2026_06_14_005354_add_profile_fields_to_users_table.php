<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('employee_id', 50)->nullable()->unique()->after('phone');
            $table->string('department', 100)->nullable()->after('employee_id');
            $table->string('position', 100)->nullable()->after('department');
            $table->timestamp('last_login_at')->nullable()->after('remember_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'employee_id', 'department', 'position', 'last_login_at']);
        });
    }
};
