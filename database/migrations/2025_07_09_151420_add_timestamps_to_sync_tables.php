<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
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
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $tableBlueprint) use ($table) {
                if (!Schema::hasColumn($table, 'created_at') && !Schema::hasColumn($table, 'updated_at')) {
                    $tableBlueprint->timestamps();
                } else {
                    if (!Schema::hasColumn($table, 'created_at')) {
                        $tableBlueprint->timestamp('created_at')->nullable();
                    }
                    if (!Schema::hasColumn($table, 'updated_at')) {
                        $tableBlueprint->timestamp('updated_at')->nullable();
                    }
                }
            });
        }
    }

    public function down(): void
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
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $tableBlueprint) use ($table) {
                if (Schema::hasColumn($table, 'created_at')) {
                    $tableBlueprint->dropColumn('created_at');
                }
                if (Schema::hasColumn($table, 'updated_at')) {
                    $tableBlueprint->dropColumn('updated_at');
                }
            });
        }
    }
};

