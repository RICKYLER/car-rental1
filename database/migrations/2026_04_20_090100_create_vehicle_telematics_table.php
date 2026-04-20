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
        Schema::create('vehicle_telematics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->timestamp('observed_at');
            $table->timestamp('received_at');
            $table->enum('connectivity_status', ['live', 'buffered', 'offline'])->default('live');
            $table->enum('battery_source', ['live', 'estimated', 'buffered'])->default('live');
            $table->unsignedSmallInteger('position_accuracy_m')->nullable();
            $table->unsignedInteger('sync_delay_seconds')->default(0);
            $table->unsignedTinyInteger('battery_soc');
            $table->unsignedInteger('estimated_range_km');
            $table->decimal('gps_latitude', 10, 7)->nullable();
            $table->decimal('gps_longitude', 10, 7)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['vehicle_id', 'observed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_telematics');
    }
};
