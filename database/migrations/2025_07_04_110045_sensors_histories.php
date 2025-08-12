<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sensors_histories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->timestamps();
            // Link to main sensor
            $table->uuid('sensor_uuid');
            $table->foreign('sensor_uuid')->references('uuid')->on('sensors_under_alerto')->onDelete('cascade');
            // Historical readings
            $table->decimal('device_water_level', 8, 2)->nullable();
            $table->decimal('device_rain_amount', 8, 2)->nullable();
            // Timestamp from when the reading was taken
            $table->timestamp('recorded_at')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sensors_histories');
    }
};
