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
        $totalOrders = SalesInvoice::count();
        $totalSales = SalesInvoice::sum('grand_total');
        $totalUsers = User::count();

        // Product category chart 
        $productStatus = Product::query()->join('categories', 'products.category_id', '=', 'categories.id')->select('categories.name as category', DB::raw('COUNT(products.id) as total'))->groupBy('categories.id', 'categories.name')->orderBy('categories.name')->get();

        // Order status chart 
        $orderStatus = SalesInvoice::query()->select('status', DB::raw('COUNT(*) as total'))->groupBy('status')->orderBy('status')->get();

        // Recent activity (last 5) 
        $recentActivities = DB::table('activity_log')->latest()->limit(5)->get();

        // Upcoming deadlines (next 7 days) 
        $upcomingOrders = SalesInvoice::query()->whereDate('invoice_date', '>=', now()->toDateString())->whereDate('invoice_date', '<=', now()->addDays(7)->toDateString())->orderBy('invoice_date')->get();

        // Product overview 
        $products = Product::with(['category', 'subCategory'])->latest()->limit(10)->get();

        return view('dashboard', compact('totalProducts', 'totalOrders', 'totalSales', 'totalUsers', 'productStatus', 'orderStatus', 'recentActivities', 'upcomingOrders', 'products'));

    }
}