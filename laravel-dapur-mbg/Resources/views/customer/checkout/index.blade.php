@extends('layouts.app')

@section('title', 'Checkout - Dapur Suplai')

@section('content')
<div class="py-12 px-4 max-w-7xl mx-auto">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-800 mb-4">Checkout Pesanan</h1>
        <p class="text-gray-600">Lengkapi informasi untuk menyelesaikan pesanan Anda</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Alamat Pengiriman</h2>
                
                <form method="POST" action="{{ route('checkout.process') }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="nama_lengkap">
                                Nama Lengkap
                            </label>
                            <input type="text" id="nama_lengkap" name="nama_lengkap" 
                                   class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-merah focus:border-transparent" 
                                   value="{{ Auth::user()->nama_lengkap ?? old('nama_lengkap') }}" required>
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="nomor_telepon">
                                Nomor Telepon
                            </label>
                            <input type="tel" id="nomor_telepon" name="nomor_telepon" 
                                   class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-merah focus:border-transparent" 
                                   value="{{ Auth::user()->nomor_telepon ?? old('nomor_telepon') }}" required>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="alamat_pengiriman">
                                Alamat Lengkap
                            </label>
                            <textarea id="alamat_pengiriman" name="alamat_pengiriman" rows="4"
                                      class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-merah focus:border-transparent" 
                                      required>{{ Auth::user()->alamat ?? old('alamat_pengiriman') }}</textarea>
                        </div>
                    </div>
                    
                    <div class="mt-8">
                        <h2 class="text-xl font-bold text-gray-800 mb-6">Metode Pembayaran</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="metode_pembayaran" value="transfer_bank" class="h-4 w-4 text-merah focus:ring-merah" required>
                                <span class="ml-3 text-gray-700">Transfer Bank</span>
                            </label>
                            
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="metode_pembayaran" value="ewallet" class="h-4 w-4 text-merah focus:ring-merah">
                                <span class="ml-3 text-gray-700">E-Wallet</span>
                            </label>
                            
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="metode_pembayaran" value="qris" class="h-4 w-4 text-merah focus:ring-merah">
                                <span class="ml-3 text-gray-700">QRIS</span>
                            </label>
                            
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="metode_pembayaran" value="cod" class="h-4 w-4 text-merah focus:ring-merah">
                                <span class="ml-3 text-gray-700">Cash on Delivery (COD)</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="mt-8">
                        <h2 class="text-xl font-bold text-gray-800 mb-6">Catatan Tambahan</h2>
                        <textarea name="catatan" rows="3"
                                  class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-merah focus:border-transparent" 
                                  placeholder="Contoh: Waktu pengiriman yang nyaman, instruksi khusus, dll.">{{ old('catatan') }}</textarea>
                    </div>
                    
                    <div class="mt-8">
                        <button type="submit" class="w-full bg-merah text-white py-4 rounded-xl font-bold hover:bg-red-700 transition shadow-lg flex items-center justify-center">
                            <span>Bayar Sekarang</span>
                            <i class="fas fa-lock ml-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-24">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Ringkasan Pesanan</h2>
                
                <div class="space-y-4 mb-6 max-h-96 overflow-y-auto pr-2">
                    @foreach($cartItems as $item)
                    <div class="flex justify-between items-center pb-4 border-b">
                        <div>
                            <h3 class="font-bold text-gray-800">{{ $item->produk->nama_produk }}</h3>
                            <p class="text-gray-600 text-sm">Qty: {{ $item->jumlah }}</p>
                        </div>
                        <p class="font-bold">Rp {{ number_format($item->produk->harga * $item->jumlah, 0, ',', '.') }}</p>
                    </div>
                    @endforeach
                </div>
                
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Biaya Pengiriman</span>
                        <span>Rp 0</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Diskon</span>
                        <span class="text-green-600">Rp 0</span>
                    </div>
                    <hr class="my-2">
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total</span>
                        <span class="text-merah">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex">
                        <i class="fas fa-info-circle text-yellow-500 mt-1 mr-3"></i>
                        <p class="text-yellow-700 text-sm">Pastikan informasi yang Anda masukkan sudah benar sebelum melanjutkan ke pembayaran.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection