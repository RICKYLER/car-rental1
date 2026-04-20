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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['pending', 'confirmed', 'active', 'completed', 'cancelled'])->default('confirmed');
            $table->string('pickup_location');
            $table->string('dropoff_location');
            $table->timestamp('start_at');
            $table->timestamp('end_at');
            $table->unsignedInteger('estimated_distance_km');
            $table->decimal('estimated_energy_kwh', 8, 1);
            $table->unsignedTinyInteger('projected_return_soc');
            $table->decimal('base_cost', 10, 2);
            $table->decimal('distance_cost', 10, 2);
            $table->decimal('energy_cost', 10, 2);
            $table->decimal('battery_wear_cost', 10, 2);
            $table->decimal('total_cost', 10, 2);
            $table->boolean('license_verified')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
