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
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('threshold_id')->constrained()->onDelete('cascade');
            $table->foreignId('response_id')->nullable()->constrained()->onDelete('set null');
            $table->string('details');
            $table->enum('status', ['pending', 'responded', 'expired'])->default('pending');
            $table->timestamp('expired_at')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); 
            $table->enum('type', ['warning', 'alert', 'critical'])->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
