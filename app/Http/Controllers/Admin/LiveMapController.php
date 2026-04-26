<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Contracts\View\View;

class LiveMapController extends Controller
{
    public function index(): View
    {
        $vehicles = Vehicle::query()
            ->orderByRaw("case status when 'available' then 0 when 'charging' then 1 when 'reserved' then 2 else 3 end")
            ->get();

        return view('admin.live-map', compact('vehicles'));
    }
}
