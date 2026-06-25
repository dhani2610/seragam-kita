<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Stats Pemasukan
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        $incomeToday = Order::where('payment_status', 'Paid')
            ->whereDate('updated_at', $today)
            ->sum('grand_total');

        $incomeMonth = Order::where('payment_status', 'Paid')
            ->whereBetween('updated_at', [$startOfMonth, Carbon::now()])
            ->sum('grand_total');

        $incomeTotal = Order::where('payment_status', 'Paid')
            ->sum('grand_total');

        // 2. Counts
        $totalCustomers = User::where('role', 'customer')->count();
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'Pending')->count();

        // 3. Data for charts or tables (latest orders)
        $latestOrders = Order::with('user')->orderBy('created_at', 'desc')->limit(5)->get();

        return view('admin.dashboard', compact(
            'incomeToday', 'incomeMonth', 'incomeTotal',
            'totalCustomers', 'totalOrders', 'pendingOrders',
            'latestOrders'
        ));
    }
}
