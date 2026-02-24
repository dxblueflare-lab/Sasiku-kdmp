<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pesanan;
use App\Models\PelacakanPesanan;
use Auth;

class PelacakanPesananController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $orders = Pesanan::where('id_user', $userId)->with('detailPesanan.produk')->paginate(10);
        
        return view('customer.orders.index', compact('orders'));
    }
    
    public function show($id)
    {
        $userId = Auth::id();
        $order = Pesanan::where('id', $id)->where('id_user', $userId)->with('detailPesanan.produk', 'user', 'pembayaran', 'pelacakanPesanan')->firstOrFail();
        
        // Dapatkan status pelacakan terbaru
        $trackingStatus = $order->pelacakanPesanan->sortByDesc('created_at')->first();
        
        return view('customer.orders.show', compact('order', 'trackingStatus'));
    }
    
    public function trackByNumber(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string'
        ]);
        
        // Kita asumsikan nomor pesanan adalah ID pesanan
        $order = Pesanan::where('id', $request->order_number)->with('detailPesanan.produk', 'user', 'pembayaran', 'pelacakanPesanan')->first();
        
        if (!$order) {
            return redirect()->back()->with('error', 'Nomor pesanan tidak ditemukan.');
        }
        
        // Jika pesanan milik pengguna saat ini atau pengguna adalah admin/supplier, izinkan akses
        if ($order->id_user == Auth::id() || Auth::user()->role === 'admin' || Auth::user()->role === 'supplier') {
            $trackingStatus = $order->pelacakanPesanan->sortByDesc('created_at')->first();
            return view('customer.orders.show', compact('order', 'trackingStatus'));
        } else {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke pesanan ini.');
        }
    }
    
    // Method untuk admin/supplier untuk memperbarui status pesanan
    public function updateStatus(Request $request, $orderId)
    {
        $user = Auth::user();
        
        // Hanya admin dan supplier yang bisa memperbarui status pesanan
        if (!in_array($user->role, ['admin', 'supplier'])) {
            abort(403);
        }
        
        $request->validate([
            'status_pesanan' => 'required|in:confirmed,processing,shipped,delivered,cancelled',
            'catatan' => 'nullable|string'
        ]);
        
        $order = Pesanan::findOrFail($orderId);
        
        // Perbarui status pesanan
        $order->update([
            'status_pesanan' => $request->status_pesanan
        ]);
        
        // Tambahkan entri pelacakan pesanan
        PelacakanPesanan::create([
            'id_pesanan' => $orderId,
            'status_pesanan' => $request->status_pesanan,
            'catatan' => $request->catatan,
        ]);
        
        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui.');
    }
}