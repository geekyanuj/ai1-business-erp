<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SalesInvoice;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Top stats
        $totalProducts = Product::count();
        $totalOrders   = SalesInvoice::count();
        $totalSales    = SalesInvoice::sum('grand_total');
        $totalUsers    = User::count();

        // Product status chart
        $productStatus = Product::select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->get();

        // Order status chart
        $orderStatus = SalesInvoice::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        // Recent activity (last 5)
        $recentActivities = DB::table('activity_log')
            ->latest()
            ->limit(5)
            ->get();

        // Upcoming deadlines (next 7 days)
        $upcomingOrders = SalesInvoice::whereDate('invoice_date', '>=', now())
            ->whereDate('invoice_date', '<=', now()->addDays(7))
            ->orderBy('invoice_date')
            ->get();

        // Product overview
        $products = Product::latest()->limit(10)->get();

        return view('dashboard', compact(
            'totalProducts',
            'totalOrders',
            'totalSales',
            'totalUsers',
            'productStatus',
            'orderStatus',
            'recentActivities',
            'upcomingOrders',
            'products'
        ));
    }
}
