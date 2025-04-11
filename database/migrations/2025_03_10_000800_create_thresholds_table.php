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
        Schema::create('thresholds', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            // $table->foreignId('sensor_id')->constrained('sensors_under_alertos')->onDelete('cascade');
            $table->morphs('sensorable');
            $table->decimal('baseline', 8,2);
            $table->decimal('sixty_percent', 8,2);
            $table->decimal('eighty_percent', 8,2);
            $table->decimal('one_hundred_percent', 8,2);
            $table->date('xs_date');
            $table->decimal('water_level', 8, 2);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thresholds');
    }
};
