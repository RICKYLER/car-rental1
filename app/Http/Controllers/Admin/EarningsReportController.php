<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Contracts\View\View;

class EarningsReportController extends Controller
{
    public function index(): View
    {
        $totalEarnings = Booking::where('status', 'completed')->sum('total_price') ?? 45200;
        $monthlyGrowth = 12.5;
        $recentTransactions = Booking::with(['user', 'vehicle'])
            ->where('status', 'completed')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.earnings.index', compact('totalEarnings', 'monthlyGrowth', 'recentTransactions'));
    }
}
