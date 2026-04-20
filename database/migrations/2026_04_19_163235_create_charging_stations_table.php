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
        Schema::create('charging_stations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location');
            $table->string('zone');
            $table->string('connector_type');
            $table->unsignedTinyInteger('total_ports');
            $table->unsignedTinyInteger('available_ports');
            $table->decimal('price_per_kwh', 10, 2);
            $table->decimal('distance_from_hub_km', 8, 1);
            $table->enum('status', ['online', 'busy', 'offline'])->default('online');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('charging_stations');
    }
};
