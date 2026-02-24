<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Keranjang;
use App\Models\Pembayaran;
use App\Models\Produk;
use Auth;

class PembayaranController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $cartItems = Keranjang::where('id_user', $userId)->with('produk')->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang Anda kosong!');
        }
        
        $total = 0;
        foreach($cartItems as $item) {
            $total += $item->produk->harga * $item->jumlah;
        }
        
        return view('customer.checkout.index', compact('cartItems', 'total'));
    }
    
    public function process(Request $request)
    {
        $userId = Auth::id();
        $cartItems = Keranjang::where('id_user', $userId)->with('produk')->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang Anda kosong!');
        }
        
        // Hitung total
        $total = 0;
        foreach($cartItems as $item) {
            $total += $item->produk->harga * $item->jumlah;
        }
        
        // Buat pesanan baru
        $pesanan = Pesanan::create([
            'id_user' => $userId,
            'total_harga' => $total,
            'status_pesanan' => 'pending',
            'metode_pembayaran' => $request->metode_pembayaran,
            'alamat_pengiriman' => $request->alamat_pengiriman,
            'catatan' => $request->catatan,
        ]);
        
        // Buat detail pesanan dan kurangi stok
        foreach($cartItems as $item) {
            DetailPesanan::create([
                'id_pesanan' => $pesanan->id,
                'id_produk' => $item->id_produk,
                'jumlah' => $item->jumlah,
                'harga_satuan' => $item->produk->harga,
                'subtotal' => $item->produk->harga * $item->jumlah,
            ]);
            
            // Kurangi stok produk
            $produk = $item->produk;
            $produk->stok -= $item->jumlah;
            $produk->save();
        }
        
        // Hapus semua item dari keranjang
        Keranjang::where('id_user', $userId)->delete();
        
        // Jika pembayaran langsung, buat entri pembayaran
        if ($request->metode_pembayaran !== 'cod') {
            Pembayaran::create([
                'id_pesanan' => $pesanan->id,
                'jumlah_pembayaran' => $total,
                'metode_pembayaran' => $request->metode_pembayaran,
                'status_pembayaran' => 'pending', // Akan diupdate setelah pembayaran diverifikasi
            ]);
        }
        
        return redirect()->route('customer.orders.show', $pesanan->id)->with('success', 'Pesanan berhasil dibuat!');
    }
    
    public function showPaymentMethods()
    {
        return view('customer.checkout.payment-methods');
    }
    
    public function verifyPayment(Request $request, $orderId)
    {
        $pesanan = Pesanan::findOrFail($orderId);
        
        // Pastikan hanya user yang membuat pesanan yang bisa mengakses
        if ($pesanan->id_user != Auth::id()) {
            abort(403);
        }
        
        // Upload bukti pembayaran jika disediakan
        if ($request->hasFile('bukti_pembayaran')) {
            $fileName = time() . '_' . $request->file('bukti_pembayaran')->getClientOriginalName();
            $filePath = $request->file('bukti_pembayaran')->storeAs('bukti_pembayaran', $fileName, 'public');
            
            // Update pesanan dengan bukti pembayaran
            $pesanan->update([
                'bukti_pembayaran' => $filePath
            ]);
            
            // Update status pembayaran
            $pembayaran = $pesanan->pembayaran;
            if ($pembayaran) {
                $pembayaran->update([
                    'bukti_pembayaran' => $filePath,
                    'status_pembayaran' => 'pending' // Menunggu verifikasi
                ]);
            }
        }
        
        return redirect()->route('customer.orders.show', $pesanan->id)->with('success', 'Bukti pembayaran berhasil dikirim dan menunggu verifikasi.');
    }
}