<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Services\RemoteCommandService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminRemoteCommandController extends Controller
{
    public function __construct(private readonly RemoteCommandService $remoteCommandService)
    {
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'command_type' => ['required', 'in:lock,unlock,immobilize'],
            'justification' => ['nullable', 'string', 'max:255'],
            'current_password' => ['required', 'string'],
        ]);

        $vehicle = Vehicle::query()->findOrFail($data['vehicle_id']);
        $verified = Hash::check($data['current_password'], $request->user()->password);
        $command = $this->remoteCommandService->issue(
            $request->user(),
            $vehicle,
            $data['command_type'],
            $data['justification'] ?? null,
            $verified,
            $request->ip(),
        );

        if (! $verified) {
            return back()
                ->withInput()
                ->withErrors([
                    'current_password' => 'Step-up verification failed. Remote command was rejected and logged.',
                ]);
        }

        return back()->with('status', ucfirst($command->command_type)." command executed for {$vehicle->name}.");
    }
}
