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
        Schema::create('staffs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('username')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('mobile_number');
            $table->foreignId('role_id')->contrained('roles')->onDelete('cascade');
            $table->foreignId('region_id')->contrained('regions')->onDelete('cascade');
            $table->foreignId('province_id')->contrained('provinces')->onDelete('cascade');
            $table->foreignId('municipality_id')->contrained('municipalities')->onDelete('cascade');
            $table->foreignId('river_id')->contrained('rivers')->onDelete('cascade');
            $table->enum('status', ['pending', 'approved', 'disabled']);
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staffs');
    }
};
