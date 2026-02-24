<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Keranjang;
use App\Models\Produk;
use Auth;

class KeranjangController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $cartItems = Keranjang::where('id_user', $userId)->with('produk.kategori')->get();
        $total = 0;
        
        foreach($cartItems as $item) {
            $total += $item->produk->harga * $item->jumlah;
        }
        
        return view('customer.cart.index', compact('cartItems', 'total'));
    }
    
    public function add(Request $request, $productId)
    {
        $userId = Auth::id();
        
        // Cek apakah produk tersedia
        $produk = Produk::findOrFail($productId);
        
        // Cek apakah produk sudah ada di keranjang
        $existingItem = Keranjang::where('id_user', $userId)
                                 ->where('id_produk', $productId)
                                 ->first();
        
        if ($existingItem) {
            // Jika sudah ada, tambahkan jumlahnya
            $existingItem->jumlah += 1;
            $existingItem->save();
        } else {
            // Jika belum ada, buat item baru
            Keranjang::create([
                'id_user' => $userId,
                'id_produk' => $productId,
                'jumlah' => 1
            ]);
        }
        
        return response()->json(['success' => true]);
    }
    
    public function remove($itemId)
    {
        $userId = Auth::id();
        
        $cartItem = Keranjang::where('id', $itemId)
                             ->where('id_user', $userId)
                             ->firstOrFail();
        
        $cartItem->delete();
        
        return redirect()->back()->with('success', 'Item berhasil dihapus dari keranjang.');
    }
    
    public function update(Request $request, $itemId)
    {
        $userId = Auth::id();
        
        $request->validate([
            'jumlah' => 'required|integer|min:1'
        ]);
        
        $cartItem = Keranjang::where('id', $itemId)
                             ->where('id_user', $userId)
                             ->firstOrFail();
        
        $cartItem->jumlah = $request->jumlah;
        $cartItem->save();
        
        return response()->json(['success' => true]);
    }
    
    public function count()
    {
        $userId = Auth::id();
        $count = Keranjang::where('id_user', $userId)->sum('jumlah');
        
        return response()->json(['count' => $count]);
    }
}