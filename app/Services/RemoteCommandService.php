<?php

namespace App\Services;

use App\Models\RemoteCommand;
use App\Models\SecurityEvent;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;

class RemoteCommandService
{
    public function issue(User $actor, Vehicle $vehicle, string $commandType, ?string $justification, bool $stepUpVerified, ?string $ipAddress = null): RemoteCommand
    {
        return DB::transaction(function () use ($actor, $vehicle, $commandType, $justification, $stepUpVerified, $ipAddress): RemoteCommand {
            $previousHash = RemoteCommand::query()->latest('id')->value('log_hash');
            $payload = [
                'vehicle_id' => $vehicle->id,
                'requested_by' => $actor->id,
                'approved_by' => $stepUpVerified ? $actor->id : null,
                'command_type' => $commandType,
                'justification' => $justification,
                'requested_ip' => $ipAddress,
                'signed_at' => $stepUpVerified ? now() : null,
                'token_expires_at' => $stepUpVerified ? now()->addMinutes(2) : null,
                'executed_at' => $stepUpVerified ? now() : null,
                'result_status' => $stepUpVerified ? 'executed' : 'rejected',
                'failure_reason' => $stepUpVerified ? null : 'Step-up verification failed.',
            ];

            $payloadChecksum = hash('sha256', json_encode($payload));
            $signature = $stepUpVerified ? hash_hmac('sha256', $payloadChecksum, (string) config('app.key')) : null;
            $logHash = hash('sha256', ($previousHash ?? 'root').'|'.$payloadChecksum.'|'.($signature ?? 'rejected'));

            $command = RemoteCommand::query()->create([
                ...$payload,
                'payload_checksum' => $payloadChecksum,
                'signature' => $signature,
                'previous_hash' => $previousHash,
                'log_hash' => $logHash,
            ]);

            if ($stepUpVerified) {
                $this->applyVehicleCommand($vehicle, $commandType);
            }

            SecurityEvent::query()->create([
                'user_id' => $actor->id,
                'actor_email' => $actor->email,
                'event_type' => $stepUpVerified ? 'remote_command_executed' : 'remote_command_rejected',
                'severity' => $stepUpVerified ? 'info' : 'critical',
                'result_status' => $stepUpVerified ? 'resolved' : 'blocked',
                'ip_address' => $ipAddress,
                'description' => $stepUpVerified
                    ? "{$commandType} command executed for {$vehicle->name}."
                    : "Rejected {$commandType} command for {$vehicle->name}.",
                'metadata' => [
                    'vehicle_id' => $vehicle->id,
                    'command_id' => $command->id,
                    'command_type' => $commandType,
                ],
                'detected_at' => now(),
            ]);

            return $command;
        });
    }

    private function applyVehicleCommand(Vehicle $vehicle, string $commandType): void
    {
        match ($commandType) {
            'lock' => $vehicle->forceFill([
                'is_locked' => true,
                'is_immobilized' => false,
            ])->save(),
            'unlock' => $vehicle->forceFill([
                'is_locked' => false,
                'is_immobilized' => false,
            ])->save(),
            'immobilize' => $vehicle->forceFill([
                'is_locked' => true,
                'is_immobilized' => true,
            ])->save(),
        };
    }
}
