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
        Schema::table('vehicles', function (Blueprint $table) {
            $table->timestamp('telematics_observed_at')->nullable()->after('next_service_due_at');
            $table->enum('connectivity_status', ['live', 'buffered', 'offline'])->default('live')->after('telematics_observed_at');
            $table->unsignedSmallInteger('position_accuracy_m')->nullable()->after('connectivity_status');
            $table->enum('battery_source', ['live', 'estimated', 'buffered'])->default('live')->after('position_accuracy_m');
            $table->unsignedInteger('sync_delay_seconds')->default(0)->after('battery_source');
            $table->boolean('is_locked')->default(true)->after('gps_longitude');
            $table->boolean('is_immobilized')->default(false)->after('is_locked');
        });

        Schema::table('charging_stations', function (Blueprint $table) {
            $table->unsignedTinyInteger('live_availability')->nullable()->after('available_ports');
            $table->unsignedSmallInteger('power_kw')->default(50)->after('status');
            $table->enum('operational_status', ['online', 'constrained', 'offline'])->default('online')->after('power_kw');
            $table->unsignedTinyInteger('confidence_score')->default(75)->after('operational_status');
            $table->boolean('is_partner_hub')->default(false)->after('confidence_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('charging_stations', function (Blueprint $table) {
            $table->dropColumn([
                'live_availability',
                'power_kw',
                'operational_status',
                'confidence_score',
                'is_partner_hub',
            ]);
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'telematics_observed_at',
                'connectivity_status',
                'position_accuracy_m',
                'battery_source',
                'sync_delay_seconds',
                'is_locked',
                'is_immobilized',
            ]);
        });
    }
};
