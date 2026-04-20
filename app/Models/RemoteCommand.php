<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RemoteCommand extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'vehicle_id',
        'requested_by',
        'approved_by',
        'command_type',
        'justification',
        'requested_ip',
        'signed_at',
        'token_expires_at',
        'executed_at',
        'result_status',
        'signature',
        'payload_checksum',
        'failure_reason',
        'previous_hash',
        'log_hash',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'signed_at' => 'datetime',
            'token_expires_at' => 'datetime',
            'executed_at' => 'datetime',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
