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
            $table->uuid('uuid')->unique();
            $table->timestamps();
            $table->morphs('sensorable');
            $table->decimal('baseline', 8,2)->nullable();
            $table->decimal('sixty_percent', 8,2)->nullable();
            $table->decimal('eighty_percent', 8,2)->nullable();
            $table->decimal('one_hundred_percent', 8,2)->nullable();
            $table->date('xs_date')->nullable();
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
