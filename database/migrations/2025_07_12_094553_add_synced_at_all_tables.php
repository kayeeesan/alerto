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
        'user_roles',
        'regions', 
        'provinces', 
        'municipalities',
        'rivers', 
        'sensors_under_alerto', 
        'sensors_under_ph',
        'responses', 
        'thresholds', 
        'alerts', 
        'user_logs',
        'staffs',  
        'notifications', 
        'series',
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
