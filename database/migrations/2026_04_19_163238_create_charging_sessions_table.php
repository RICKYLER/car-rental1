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
        Schema::create('charging_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('charging_station_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['scheduled', 'in_progress', 'completed'])->default('scheduled');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('expected_completion_at');
            $table->timestamp('ended_at')->nullable();
            $table->unsignedTinyInteger('current_soc');
            $table->unsignedTinyInteger('target_soc');
            $table->decimal('energy_kwh', 8, 1);
            $table->decimal('estimated_cost', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('charging_sessions');
    }
};
