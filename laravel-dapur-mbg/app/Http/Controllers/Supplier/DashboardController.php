<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $totalProducts = Produk::where('id_supplier', $userId)->count();
        $totalOrders = DetailPesanan::whereIn('id_produk', function($query) use ($userId) {
            $query->select('id')->from('produk')->where('id_supplier', $userId);
        })->distinct('id_pesanan')->count();
        
        $recentOrders = Pesanan::whereIn('id', function($query) use ($userId) {
            $query->select('id_pesanan')->from('detail_pesanan')->whereIn('id_produk', function($subQuery) use ($userId) {
                $subQuery->select('id')->from('produk')->where('id_supplier', $userId);
            });
        })->latest()->take(5)->get();
        
        return view('supplier.dashboard', compact('totalProducts', 'totalOrders', 'recentOrders'));
    }
    
    public function products()
    {
        $userId = Auth::id();
        $products = Produk::where('id_supplier', $userId)->with('kategori')->paginate(10);
        return view('supplier.products.index', compact('products'));
    }
    
    public function orders()
    {
        $userId = Auth::id();
        $orders = Pesanan::whereIn('id', function($query) use ($userId) {
            $query->select('id_pesanan')->from('detail_pesanan')->whereIn('id_produk', function($subQuery) use ($userId) {
                $subQuery->select('id')->from('produk')->where('id_supplier', $userId);
            });
        })->with('user', 'detailPesanan')->paginate(10);
        
        return view('supplier.orders.index', compact('orders'));
    }
}