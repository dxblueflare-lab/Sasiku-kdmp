@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->id . ' - Dapur Suplai')

@section('content')
<div class="py-12 px-4 max-w-7xl mx-auto">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-800 mb-4">Detail Pesanan #{{ $order->id }}</h1>
        <p class="text-gray-600">Informasi lengkap tentang pesanan Anda</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8">
            <!-- Order Information -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Informasi Pesanan</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-gray-600 text-sm">Tanggal Pesanan</p>
                        <p class="font-bold text-gray-800">{{ $order->created_at->format('d M Y H:i') }}</p>
                    </div>
                    
                    <div>
                        <p class="text-gray-600 text-sm">Status Pesanan</p>
                        <p class="font-bold text-gray-800">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold 
                                @if($order->status_pesanan == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order->status_pesanan == 'confirmed') bg-blue-100 text-blue-800
                                @elseif($order->status_pesanan == 'processing') bg-purple-100 text-purple-800
                                @elseif($order->status_pesanan == 'shipped') bg-indigo-100 text-indigo-800
                                @elseif($order->status_pesanan == 'delivered') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $order->status_pesanan)) }}
                            </span>
                        </p>
                    </div>
                    
                    <div>
                        <p class="text-gray-600 text-sm">Metode Pembayaran</p>
                        <p class="font-bold text-gray-800">{{ $order->metode_pembayaran ? ucfirst(str_replace('_', ' ', $order->metode_pembayaran)) : 'Belum ditentukan' }}</p>
                    </div>
                    
                    <div>
                        <p class="text-gray-600 text-sm">Total Pembayaran</p>
                        <p class="font-bold text-gray-800 text-merah">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</p>
                    </div>
                </div>
                
                @if($order->catatan)
                <div class="mt-6">
                    <p class="text-gray-600 text-sm">Catatan</p>
                    <p class="font-bold text-gray-800">{{ $order->catatan }}</p>
                </div>
                @endif
            </div>
            
            <!-- Order Items -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Item dalam Pesanan</h2>
                
                <div class="space-y-4">
                    @foreach($order->detailPesanan as $item)
                    <div class="flex items-center border-b pb-4 last:border-0 last:pb-0">
                        <div class="flex-shrink-0 w-16 h-16 bg-gray-200 rounded-lg overflow-hidden">
                            <img src="{{ $item->produk->gambar_produk ? asset('storage/' . $item->produk->gambar_produk) : 'https://placehold.co/100x100?text=No+Image' }}" 
                                 alt="{{ $item->produk->nama_produk }}" 
                                 class="w-full h-full object-cover">
                        </div>
                        
                        <div class="ml-4 flex-grow">
                            <h3 class="font-bold text-gray-800">{{ $item->produk->nama_produk }}</h3>
                            <p class="text-gray-600 text-sm">Jumlah: {{ $item->jumlah }}</p>
                        </div>
                        
                        <div class="text-right">
                            <p class="font-bold text-gray-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                            <p class="text-gray-600 text-sm">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}/item</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Shipping Address -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Alamat Pengiriman</h2>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-gray-800">{{ $order->alamat_pengiriman }}</p>
                </div>
            </div>
        </div>
        
        <div class="lg:col-span-1">
            <!-- Order Tracking -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 sticky top-24">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Status Pesanan</h2>
                
                <div class="text-center mb-6">
                    <div class="w-24 h-24 rounded-full bg-merah/10 flex items-center justify-center mx-auto mb-4">
                        <i class="fas 
                            @if($order->status_pesanan == 'pending') fa-clock text-yellow-500
                            @elseif($order->status_pesanan == 'confirmed') fa-check-circle text-blue-500
                            @elseif($order->status_pesanan == 'processing') fa-cogs text-purple-500
                            @elseif($order->status_pesanan == 'shipped') fa-truck text-indigo-500
                            @elseif($order->status_pesanan == 'delivered') fa-check-circle text-green-500
                            @else fa-times-circle text-red-500 @endif 
                            text-3xl"></i>
                    </div>
                    <p class="text-lg font-bold text-gray-800">{{ ucfirst(str_replace('_', ' ', $order->status_pesanan)) }}</p>
                    <p class="text-gray-600 text-sm">Terakhir diperbarui: {{ $order->updated_at->format('d M Y H:i') }}</p>
                </div>
                
                <div class="space-y-4">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-merah flex items-center justify-center text-white text-sm">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="font-bold text-gray-800">Pesanan Diterima</p>
                            <p class="text-gray-600 text-sm">{{ $order->created_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full 
                            @if(in_array($order->status_pesanan, ['confirmed', 'processing', 'shipped', 'delivered'])) bg-merah text-white
                            @else bg-gray-200 text-gray-500 @endif 
                            flex items-center justify-center text-sm">
                            <i class="fas 
                                @if(in_array($order->status_pesanan, ['confirmed', 'processing', 'shipped', 'delivered'])) fa-check
                                @else fa-clock @endif"></i>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="font-bold text-gray-800">Dikonfirmasi</p>
                            <p class="text-gray-600 text-sm">
                                @if($order->status_pesanan === 'pending')
                                    Menunggu konfirmasi
                                @else
                                    {{ $order->created_at->format('d M Y H:i') }}
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full 
                            @if(in_array($order->status_pesanan, ['processing', 'shipped', 'delivered'])) bg-merah text-white
                            @else bg-gray-200 text-gray-500 @endif 
                            flex items-center justify-center text-sm">
                            <i class="fas 
                                @if(in_array($order->status_pesanan, ['processing', 'shipped', 'delivered'])) fa-check
                                @else fa-clock @endif"></i>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="font-bold text-gray-800">Diproses</p>
                            <p class="text-gray-600 text-sm">
                                @if(!in_array($order->status_pesanan, ['processing', 'shipped', 'delivered']))
                                    Menunggu proses
                                @else
                                    {{ $order->created_at->format('d M Y H:i') }}
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full 
                            @if(in_array($order->status_pesanan, ['shipped', 'delivered'])) bg-merah text-white
                            @else bg-gray-200 text-gray-500 @endif 
                            flex items-center justify-center text-sm">
                            <i class="fas 
                                @if(in_array($order->status_pesanan, ['shipped', 'delivered'])) fa-check
                                @else fa-clock @endif"></i>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="font-bold text-gray-800">Dikirim</p>
                            <p class="text-gray-600 text-sm">
                                @if(!in_array($order->status_pesanan, ['shipped', 'delivered']))
                                    Menunggu pengiriman
                                @else
                                    {{ $order->created_at->format('d M Y H:i') }}
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full 
                            @if($order->status_pesanan === 'delivered') bg-merah text-white
                            @else bg-gray-200 text-gray-500 @endif 
                            flex items-center justify-center text-sm">
                            <i class="fas 
                                @if($order->status_pesanan === 'delivered') fa-check
                                @else fa-clock @endif"></i>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="font-bold text-gray-800">Diterima</p>
                            <p class="text-gray-600 text-sm">
                                @if($order->status_pesanan !== 'delivered')
                                    Menunggu penerimaan
                                @else
                                    {{ $order->updated_at->format('d M Y H:i') }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                
                @if($order->status_pesanan === 'shipped' || $order->status_pesanan === 'processing')
                <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-blue-800 font-bold flex items-center">
                        <i class="fas fa-info-circle mr-2"></i> Informasi Pengiriman
                    </p>
                    <p class="text-blue-700 text-sm mt-1">Pesanan Anda sedang dalam proses pengiriman. Silakan pantau status secara berkala.</p>
                </div>
                @endif
            </div>
            
            <!-- Payment Info -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Ringkasan Pembayaran</h2>
                
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
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
                        <span class="text-merah">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                    </div>
                </div>
                
                @if($order->bukti_pembayaran)
                <div class="mt-6">
                    <p class="text-gray-600 text-sm mb-2">Bukti Pembayaran</p>
                    <img src="{{ asset('storage/' . $order->bukti_pembayaran) }}" alt="Bukti Pembayaran" class="w-full rounded-lg border">
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection