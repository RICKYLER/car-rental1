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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('brand');
            $table->string('model');
            $table->unsignedSmallInteger('year');
            $table->string('plate_number')->unique();
            $table->enum('status', ['available', 'reserved', 'charging', 'maintenance'])->default('available');
            $table->unsignedTinyInteger('battery_soc');
            $table->unsignedTinyInteger('battery_health');
            $table->unsignedInteger('estimated_range_km');
            $table->decimal('battery_capacity_kwh', 6, 1);
            $table->string('connector_type');
            $table->string('location_zone');
            $table->decimal('daily_rate', 10, 2);
            $table->decimal('per_km_rate', 10, 2);
            $table->decimal('energy_rate', 10, 2);
            $table->unsignedInteger('odometer_km')->default(0);
            $table->text('description')->nullable();
            $table->string('accent_color')->default('#2dd4bf');
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('last_service_at')->nullable();
            $table->timestamp('next_service_due_at')->nullable();
            $table->decimal('gps_latitude', 10, 7)->nullable();
            $table->decimal('gps_longitude', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
