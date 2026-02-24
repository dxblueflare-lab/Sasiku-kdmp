@extends('layouts.app')

@section('title', 'Pesanan Saya - Dapur Suplai')

@section('content')
<div class="py-12 px-4 max-w-7xl mx-auto">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-800 mb-4">Riwayat Pesanan</h1>
        <p class="text-gray-600">Lacak status pesanan Anda</p>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Lacak Pesanan</h2>
        <div class="max-w-2xl">
            <form method="GET" action="{{ route('track.by.number') }}" class="flex shadow-lg rounded-full overflow-hidden">
                <input type="text" name="order_number" placeholder="Masukkan nomor pesanan (contoh: 1, 2, dst)" class="flex-1 px-6 py-4 outline-none text-gray-700">
                <button type="submit" class="bg-merah text-white px-8 py-4 font-semibold hover:bg-red-700 transition flex items-center space-x-2">
                    <i class="fas fa-search"></i>
                    <span>Lacak</span>
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="p-6 border-b">
            <h2 class="text-xl font-bold text-gray-800">Daftar Pesanan</h2>
        </div>
        
        @if($orders->count() > 0)
        <div class="divide-y">
            @foreach($orders as $order)
            <div class="p-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between">
                    <div>
                        <h3 class="font-bold text-lg text-gray-800">Pesanan #{{ $order->id }}</h3>
                        <p class="text-gray-600 text-sm">Dibuat pada {{ $order->created_at->format('d M Y H:i') }}</p>
                    </div>
                    
                    <div class="mt-2 md:mt-0">
                        <span class="px-3 py-1 rounded-full text-sm font-semibold 
                            @if($order->status_pesanan == 'pending') bg-yellow-100 text-yellow-800
                            @elseif($order->status_pesanan == 'confirmed') bg-blue-100 text-blue-800
                            @elseif($order->status_pesanan == 'processing') bg-purple-100 text-purple-800
                            @elseif($order->status_pesanan == 'shipped') bg-indigo-100 text-indigo-800
                            @elseif($order->status_pesanan == 'delivered') bg-green-100 text-green-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst(str_replace('_', ' ', $order->status_pesanan)) }}
                        </span>
                        <p class="text-gray-800 font-bold mt-1">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</p>
                    </div>
                </div>
                
                <div class="mt-4">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full 
                            @if($order->status_pesanan == 'pending') bg-yellow-500" style="width: 10%
                            @elseif($order->status_pesanan == 'confirmed') bg-blue-500" style="width: 30%
                            @elseif($order->status_pesanan == 'processing') bg-purple-500" style="width: 50%
                            @elseif($order->status_pesanan == 'shipped') bg-indigo-500" style="width: 80%
                            @elseif($order->status_pesanan == 'delivered') bg-green-500" style="width: 100%
                            @else bg-red-500" style="width: 100% @endif">
                        </div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 mt-2">
                        <span>Pesanan Diterima</span>
                        <span>Dikonfirmasi</span>
                        <span>Diproses</span>
                        <span>Dikirim</span>
                        <span>Diterima</span>
                    </div>
                </div>
                
                <div class="mt-4 flex flex-wrap gap-2">
                    <a href="{{ route('customer.orders.show', $order->id) }}" class="bg-merah text-white px-4 py-2 rounded-full hover:bg-red-700 transition text-sm">
                        Lihat Detail
                    </a>
                    
                    @if($order->status_pesanan === 'delivered')
                    <button class="bg-gray-200 text-gray-800 px-4 py-2 rounded-full hover:bg-gray-300 transition text-sm">
                        Beri Ulasan
                    </button>
                    @endif
                    
                    @if($order->status_pesanan !== 'delivered' && $order->status_pesanan !== 'cancelled')
                    <button class="bg-gray-100 text-gray-800 px-4 py-2 rounded-full hover:bg-gray-200 transition text-sm border">
                        Batalkan Pesanan
                    </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="p-6 border-t">
            {{ $orders->links() }}
        </div>
        @else
        <div class="p-12 text-center">
            <i class="fas fa-inbox text-6xl text-gray-300 mb-6"></i>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Anda belum memiliki pesanan</h3>
            <p class="text-gray-600 mb-6">Pesanan Anda akan muncul di sini setelah Anda melakukan pembelian</p>
            <a href="{{ url('/products') }}" class="bg-merah text-white px-6 py-3 rounded-full font-bold hover:bg-red-700 transition">
                Mulai Belanja
            </a>
        </div>
        @endif
    </div>
</div>
@endsection