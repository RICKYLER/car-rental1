<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;

class CustomerManagementController extends Controller
{
    public function index(): View
    {
        $customers = User::where('role', 'customer')
            ->withCount('bookings')
            ->latest()
            ->paginate(15);

        return view('admin.customers.index', compact('customers'));
    }
}
