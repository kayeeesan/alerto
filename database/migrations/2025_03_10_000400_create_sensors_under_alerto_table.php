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
        Schema::create('sensors_under_alerto', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->timestamps();
            $table->string('name');
            $table->string('device_id');
            $table->string('device_water_level')->nullable();
            $table->string('device_rain_amount')->nullable();
            $table->float('previous_water_level')->nullable();
            $table->float('previous_rain_amount')->nullable();
            $table->foreignId('river_id')->constrained('rivers')->onDelete('cascade');
            $table->foreignId('municipality_id')->constrained('municipalities')->onDelete('cascade');
            $table->decimal('long', 10, 6)->nullable();
            $table->decimal('lat', 10, 6)->nullable();
            $table->string('status')->nullable();
            $table->string('sensor_type');
            $table->timestamp('api_last_updated_at')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensors_under_alerto');
    }
};
