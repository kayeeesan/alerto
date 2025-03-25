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
            $table->string('middle_name')->nullable();
            $table->string('mobile_number')->nullable();
            $table->foreignId('role_id')->constrained('role')->onDelete('cascade');
            $table->string('government_agency');
            $table->foreignId('region_id')->constrained('region')->onDelete('cascade');
            $table->foreignId('province_id')->constrained('province')->onDelete('cascade');
            $table->foreignId('municipality_id')->constrained('municipality')->onDelete('cascade');
            $table->foreignId('river_id')->constrained('river')->onDelete('cascade');
            $table->string('lgu_fb')->nullable();
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
