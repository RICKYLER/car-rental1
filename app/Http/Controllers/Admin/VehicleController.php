<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function create()
    {
        return view('admin.vehicles.create');
    }

    public function store(Request $request)
    {
        // Validation and storage logic would go here
        // For now, we'll just redirect back with a success message
        return redirect()->route('fleet.index')->with('success', 'Vehicle added successfully!');
    }
}
