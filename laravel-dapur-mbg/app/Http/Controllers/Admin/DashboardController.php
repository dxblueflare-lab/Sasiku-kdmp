<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Produk;
use App\Models\Pesanan;
use App\Models\KategoriProduk;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalProducts = Produk::count();
        $totalOrders = Pesanan::count();
        $pendingOrders = Pesanan::where('status_pesanan', 'pending')->count();
        
        return view('admin.dashboard', compact('totalUsers', 'totalProducts', 'totalOrders', 'pendingOrders'));
    }
    
    public function products()
    {
        $products = Produk::with('kategori', 'supplier')->paginate(10);
        return view('admin.products.index', compact('products'));
    }
    
    public function orders()
    {
        $orders = Pesanan::with('user')->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }
    
    public function users()
    {
        $users = User::paginate(10);
        return view('admin.users.index', compact('users'));
    }
}