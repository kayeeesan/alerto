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
        $tables = [
        'users', 
        'roles', 
        'regions', 
        'provinces', 
        'municipalities',
        'rivers', 
        'sensors_under_alertos', 
        'sensors_under_phs',
        'responses', 
        'thresholds', 
        'alerts', 
        'user_logs',
        'staffs', 
        'contact_messages', 
        'notifications', 
        'series', 
        'user_roles',
    ];

    foreach ($tables as $table) {
        Schema::table($table, function (Blueprint $table) {
            $table->timestamp('synced_at')->nullable()->after('updated_at');
        });
    }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('synced_at');
            });
        }
    }
};
