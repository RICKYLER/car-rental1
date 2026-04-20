<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'brand',
        'model',
        'year',
        'plate_number',
        'status',
        'battery_soc',
        'battery_health',
        'estimated_range_km',
        'battery_capacity_kwh',
        'connector_type',
        'location_zone',
        'daily_rate',
        'per_km_rate',
        'energy_rate',
        'odometer_km',
        'description',
        'accent_color',
        'last_seen_at',
        'last_service_at',
        'next_service_due_at',
        'gps_latitude',
        'gps_longitude',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'daily_rate' => 'decimal:2',
            'per_km_rate' => 'decimal:2',
            'energy_rate' => 'decimal:2',
            'battery_capacity_kwh' => 'decimal:1',
            'last_seen_at' => 'datetime',
            'last_service_at' => 'datetime',
            'next_service_due_at' => 'datetime',
            'gps_latitude' => 'float',
            'gps_longitude' => 'float',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function chargingSessions(): HasMany
    {
        return $this->hasMany(ChargingSession::class);
    }
}
