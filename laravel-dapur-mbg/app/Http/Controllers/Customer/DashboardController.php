<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Keranjang;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $totalOrders = Pesanan::where('id_user', $userId)->count();
        $recentOrders = Pesanan::where('id_user', $userId)->latest()->take(5)->get();
        
        return view('customer.dashboard', compact('totalOrders', 'recentOrders'));
    }
    
    public function products()
    {
        $products = Produk::with('kategori', 'supplier')->paginate(12);
        return view('customer.products.index', compact('products'));
    }
    
    public function cart()
    {
        $userId = Auth::id();
        $cartItems = Keranjang::where('id_user', $userId)->with('produk.kategori')->get();
        $total = 0;
        
        foreach($cartItems as $item) {
            $total += $item->produk->harga * $item->jumlah;
        }
        
        return view('customer.cart.index', compact('cartItems', 'total'));
    }
    
    public function checkout()
    {
        $userId = Auth::id();
        $cartItems = Keranjang::where('id_user', $userId)->with('produk')->get();
        $total = 0;
        
        foreach($cartItems as $item) {
            $total += $item->produk->harga * $item->jumlah;
        }
        
        return view('customer.checkout.index', compact('cartItems', 'total'));
    }
    
    public function orders()
    {
        $userId = Auth::id();
        $orders = Pesanan::where('id_user', $userId)->with('detailPesanan.produk')->paginate(10);
        return view('customer.orders.index', compact('orders'));
    }
}