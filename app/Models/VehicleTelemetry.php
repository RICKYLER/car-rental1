<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleTelemetry extends Model
{
    protected $table = 'vehicle_telematics';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'vehicle_id',
        'observed_at',
        'received_at',
        'connectivity_status',
        'battery_source',
        'position_accuracy_m',
        'sync_delay_seconds',
        'battery_soc',
        'estimated_range_km',
        'gps_latitude',
        'gps_longitude',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'observed_at' => 'datetime',
            'received_at' => 'datetime',
            'position_accuracy_m' => 'integer',
            'sync_delay_seconds' => 'integer',
            'gps_latitude' => 'float',
            'gps_longitude' => 'float',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
