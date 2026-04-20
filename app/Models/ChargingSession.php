<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChargingSession extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'vehicle_id',
        'charging_station_id',
        'status',
        'started_at',
        'expected_completion_at',
        'ended_at',
        'current_soc',
        'target_soc',
        'energy_kwh',
        'estimated_cost',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'expected_completion_at' => 'datetime',
            'ended_at' => 'datetime',
            'energy_kwh' => 'decimal:1',
            'estimated_cost' => 'decimal:2',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function chargingStation(): BelongsTo
    {
        return $this->belongsTo(ChargingStation::class);
    }
}
