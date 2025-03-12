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
               // Foreign keys
           $table->foreignId('river_id')->constrained('rivers')->onDelete('cascade');
           $table->foreignId('sensor_id')->constrained('sensors_under_alertos')->onDelete('cascade');
           $table->foreignId('municipality_id')->constrained('municipalities')->onDelete('cascade');
            // Extra fields
            $table->date('xs_date');
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
