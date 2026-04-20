<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Booking extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'reference',
        'user_id',
        'vehicle_id',
        'status',
        'pickup_location',
        'dropoff_location',
        'start_at',
        'end_at',
        'estimated_distance_km',
        'estimated_energy_kwh',
        'projected_return_soc',
        'base_cost',
        'distance_cost',
        'energy_cost',
        'battery_wear_cost',
        'total_cost',
        'license_verified',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'estimated_energy_kwh' => 'decimal:1',
            'base_cost' => 'decimal:2',
            'distance_cost' => 'decimal:2',
            'energy_cost' => 'decimal:2',
            'battery_wear_cost' => 'decimal:2',
            'total_cost' => 'decimal:2',
            'license_verified' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $booking): void {
            if (! $booking->reference) {
                $booking->reference = 'ECROS-'.Str::upper(Str::random(6));
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
