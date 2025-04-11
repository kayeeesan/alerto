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
        Schema::create('sensors_under_phs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
                 // Foreign keys
           $table->foreignId('river_id')->constrained('rivers')->onDelete('cascade');
           $table->foreignId('municipality_id')->constrained('municipalities')->onDelete('cascade');
           $table->decimal('long', 8, 2);
           $table->decimal('lat', 8, 2);
           $table->enum('status', ['enabled','disabled']);
           $table->enum('sensor_type', ['ARG', 'WLMS', 'TANDEM']);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensors_under_phs');
    }
};
