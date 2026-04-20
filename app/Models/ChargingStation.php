<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChargingStation extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'location',
        'zone',
        'connector_type',
        'total_ports',
        'available_ports',
        'price_per_kwh',
        'distance_from_hub_km',
        'status',
        'latitude',
        'longitude',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price_per_kwh' => 'decimal:2',
            'distance_from_hub_km' => 'decimal:1',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function chargingSessions(): HasMany
    {
        return $this->hasMany(ChargingSession::class);
    }
}
