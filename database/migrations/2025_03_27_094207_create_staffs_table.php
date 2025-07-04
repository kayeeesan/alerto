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
            // $table->string('username')->unique();
            // $table->string('first_name');
            // $table->string('last_name');
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('mobile_number');
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('region_id')->constrained('regions')->onDelete('cascade');
            $table->foreignId('province_id')->constrained('provinces')->onDelete('cascade');
            $table->foreignId('municipality_id')->constrained('municipalities')->onDelete('cascade');
            $table->foreignId('river_id')->constrained('rivers')->onDelete('cascade');
            $table->string('fb_lgu');
            
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
