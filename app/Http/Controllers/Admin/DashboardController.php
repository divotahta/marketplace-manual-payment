<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Transaksi;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik dasar
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        
        // Transaksi hari ini
        $todayTransactions = Transaksi::whereDate('created_at', today())->count();
        
        // Pendapatan hari ini (hanya dari transaksi yang sudah selesai)
        $todayRevenue = Transaksi::whereDate('created_at', today())
            ->where('payment_status', 'selesai')
            ->sum('total_price');

        // Transaksi terbaru
        $recentTransactions = Transaksi::with(['product'])
            ->latest()
            ->limit(5)
            ->get();

        // Produk terlaris
        $topProducts = Product::withCount(['transaksis as total_sold' => function($query) {
                $query->where('payment_status', 'selesai');
            }])
            ->with('category')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalCategories',
            'todayTransactions',
            'todayRevenue',
            'recentTransactions',
            'topProducts'
        ));
    }
} 