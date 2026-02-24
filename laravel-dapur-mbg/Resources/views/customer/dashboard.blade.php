@extends('layouts.app')

@section('title', 'Dashboard Customer - Dapur Suplai')

@section('content')
<div class="py-12 px-4 max-w-7xl mx-auto">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-800 mb-4">Dashboard Customer</h1>
        <p class="text-gray-600">Kelola pesanan dan pengaturan akun Anda</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
        <div class="bg-white rounded-2xl shadow-lg p-6 card-hover">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-shopping-cart text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500">Jumlah Pesanan</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalOrders }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6 card-hover">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-shopping-bag text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500">Status Akun</p>
                    <p class="text-2xl font-bold text-gray-800">Aktif</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <a href="{{ route('customer.products') }}" class="bg-white rounded-2xl shadow-lg p-6 card-hover flex items-center">
            <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                <i class="fas fa-store text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="font-bold text-lg text-gray-800">Belanja Sekarang</h3>
                <p class="text-gray-600">Lihat produk tersedia</p>
            </div>
        </a>

        <a href="{{ route('customer.cart') }}" class="bg-white rounded-2xl shadow-lg p-6 card-hover flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                <i class="fas fa-shopping-basket text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="font-bold text-lg text-gray-800">Lihat Keranjang</h3>
                <p class="text-gray-600">Periksa barang di keranjang</p>
            </div>
        </a>

        <a href="{{ route('customer.orders') }}" class="bg-white rounded-2xl shadow-lg p-6 card-hover flex items-center">
            <div class="p-3 rounded-full bg-pink-100 text-pink-600">
                <i class="fas fa-receipt text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="font-bold text-lg text-gray-800">Riwayat Pesanan</h3>
                <p class="text-gray-600">Lihat pesanan sebelumnya</p>
            </div>
        </a>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Pesanan Terbaru</h2>
        @if($recentOrders->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Pesanan</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentOrders as $order)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $order->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($order->status_pesanan == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order->status_pesanan == 'confirmed') bg-blue-100 text-blue-800
                                @elseif($order->status_pesanan == 'processing') bg-purple-100 text-purple-800
                                @elseif($order->status_pesanan == 'shipped') bg-indigo-100 text-indigo-800
                                @elseif($order->status_pesanan == 'delivered') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $order->status_pesanan)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('customer.orders.show', $order->id) }}" class="text-merah hover:text-red-700">Lihat Detail</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-8">
            <i class="fas fa-inbox text-5xl text-gray-300 mb-4"></i>
            <p class="text-gray-500">Anda belum memiliki pesanan</p>
            <a href="{{ route('customer.products') }}" class="mt-4 inline-block bg-merah text-white px-6 py-2 rounded-full hover:bg-red-700 transition">Mulai Belanja</a>
        </div>
        @endif
    </div>
</div>
@endsection