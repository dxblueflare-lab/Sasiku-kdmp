@extends('layouts.app')

@section('title', 'Dashboard Admin - Dapur Suplai')

@section('content')
<div class="py-12 px-4 max-w-7xl mx-auto">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-800 mb-4">Dashboard Administrator</h1>
        <p class="text-gray-600">Kelola sistem e-commerce Dapur Suplai</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <div class="bg-white rounded-2xl shadow-lg p-6 card-hover">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500">Total Pengguna</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalUsers }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6 card-hover">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-box text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500">Total Produk</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalProducts }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6 card-hover">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-shopping-cart text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500">Total Pesanan</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalOrders }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6 card-hover">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500">Pesanan Pending</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $pendingOrders }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <a href="{{ route('admin.products') }}" class="bg-white rounded-2xl shadow-lg p-6 card-hover flex items-center">
            <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                <i class="fas fa-box-open text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="font-bold text-lg text-gray-800">Kelola Produk</h3>
                <p class="text-gray-600">Tambah, edit, atau hapus produk</p>
            </div>
        </a>

        <a href="{{ route('admin.orders') }}" class="bg-white rounded-2xl shadow-lg p-6 card-hover flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                <i class="fas fa-receipt text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="font-bold text-lg text-gray-800">Kelola Pesanan</h3>
                <p class="text-gray-600">Lihat dan proses pesanan</p>
            </div>
        </a>

        <a href="{{ route('admin.users') }}" class="bg-white rounded-2xl shadow-lg p-6 card-hover flex items-center">
            <div class="p-3 rounded-full bg-pink-100 text-pink-600">
                <i class="fas fa-users text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="font-bold text-lg text-gray-800">Kelola Pengguna</h3>
                <p class="text-gray-600">Atur akun pengguna</p>
            </div>
        </a>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Aktivitas Terbaru</h2>
        <div class="space-y-4">
            <div class="flex items-center border-b pb-4">
                <div class="p-2 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-plus"></i>
                </div>
                <div class="ml-4">
                    <p class="font-semibold">Produk baru ditambahkan</p>
                    <p class="text-gray-600 text-sm">Beras Premium oleh Supplier A</p>
                </div>
                <div class="ml-auto text-gray-500 text-sm">2 jam yang lalu</div>
            </div>
            
            <div class="flex items-center border-b pb-4">
                <div class="p-2 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="ml-4">
                    <p class="font-semibold">Pesanan baru diterima</p>
                    <p class="text-gray-600 text-sm">Pesanan #SPPG-2024-001</p>
                </div>
                <div class="ml-auto text-gray-500 text-sm">4 jam yang lalu</div>
            </div>
            
            <div class="flex items-center border-b pb-4">
                <div class="p-2 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="ml-4">
                    <p class="font-semibold">Pengguna baru terdaftar</p>
                    <p class="text-gray-600 text-sm">John Doe sebagai Customer</p>
                </div>
                <div class="ml-auto text-gray-500 text-sm">6 jam yang lalu</div>
            </div>
        </div>
    </div>
</div>
@endsection