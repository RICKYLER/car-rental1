<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index()
    {
        // For demo purposes, we'll pick vehicles with battery health < 90 or just a random selection
        $maintenanceVehicles = Vehicle::where('battery_health', '<', 90)
            ->orWhereIn('status', ['maintenance', 'charging'])
            ->take(10)
            ->get();

        return view('admin.maintenance', compact('maintenanceVehicles'));
    }
}
