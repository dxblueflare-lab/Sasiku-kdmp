@extends('layouts.app')

@section('title', 'Keranjang Belanja - Dapur Suplai')

@section('content')
<div class="py-12 px-4 max-w-7xl mx-auto">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-800 mb-4">Keranjang Belanja</h1>
        <p class="text-gray-600">Periksa dan kelola barang-barang dalam keranjang Anda</p>
    </div>

    @if($cartItems->count() > 0)
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="p-6 border-b">
                    <h2 class="text-xl font-bold text-gray-800">Daftar Barang</h2>
                </div>
                
                <div class="divide-y">
                    @foreach($cartItems as $item)
                    <div class="p-6 flex items-center">
                        <div class="flex-shrink-0 w-24 h-24 bg-gray-200 rounded-lg overflow-hidden">
                            <img src="{{ $item->produk->gambar_produk ? asset('storage/' . $item->produk->gambar_produk) : 'https://placehold.co/100x100?text=No+Image' }}" 
                                 alt="{{ $item->produk->nama_produk }}" 
                                 class="w-full h-full object-cover">
                        </div>
                        
                        <div class="ml-6 flex-grow">
                            <h3 class="font-bold text-lg text-gray-800">{{ $item->produk->nama_produk }}</h3>
                            <p class="text-gray-600 text-sm">{{ $item->produk->kategori->nama_kategori ?? 'Umum' }}</p>
                            <p class="text-merah font-bold mt-1">Rp {{ number_format($item->produk->harga, 0, ',', '.') }}</p>
                        </div>
                        
                        <div class="flex items-center">
                            <div class="flex items-center border rounded-lg">
                                <button onclick="updateQuantity({{ $item->id }}, -1)" class="px-3 py-1 text-gray-600 hover:bg-gray-100">-</button>
                                <input type="number" id="qty-{{ $item->id }}" value="{{ $item->jumlah }}" min="1" 
                                       class="w-16 text-center border-y" readonly>
                                <button onclick="updateQuantity({{ $item->id }}, 1)" class="px-3 py-1 text-gray-600 hover:bg-gray-100">+</button>
                            </div>
                            
                            <div class="ml-6 text-right">
                                <p class="font-bold text-gray-800">Rp {{ number_format($item->produk->harga * $item->jumlah, 0, ',', '.') }}</p>
                                <a href="{{ route('cart.remove', $item->id) }}" 
                                   class="text-red-500 hover:text-red-700 text-sm mt-2 inline-block"
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus item ini dari keranjang?')">Hapus</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-24">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Ringkasan Belanja</h2>
                
                <div class="space-y-4 mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-bold">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Biaya Pengiriman</span>
                        <span class="font-bold">Rp 0</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Diskon</span>
                        <span class="font-bold text-green-600">Rp 0</span>
                    </div>
                    <hr class="my-2">
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total</span>
                        <span class="text-merah">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>
                
                <a href="{{ route('checkout') }}" class="w-full bg-merah text-white py-3 rounded-xl font-bold hover:bg-red-700 transition shadow-lg flex items-center justify-center">
                    <span>Lanjutkan ke Pembayaran</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
                
                <a href="{{ url('/products') }}" class="w-full mt-4 text-center bg-gray-200 text-gray-800 py-3 rounded-xl font-bold hover:bg-gray-300 transition">
                    Lanjut Belanja
                </a>
            </div>
        </div>
    </div>
    @else
    <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
        <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-6"></i>
        <h3 class="text-2xl font-bold text-gray-800 mb-2">Keranjang Anda Kosong</h3>
        <p class="text-gray-600 mb-6">Tambahkan beberapa produk ke keranjang Anda sekarang</p>
        <a href="{{ url('/products') }}" class="bg-merah text-white px-8 py-3 rounded-full font-bold hover:bg-red-700 transition">Mulai Belanja</a>
    </div>
    @endif
</div>

<script>
    function updateQuantity(itemId, change) {
        const qtyElement = document.getElementById(`qty-${itemId}`);
        let newQty = parseInt(qtyElement.value) + change;
        
        if (newQty < 1) newQty = 1;
        
        // Kirim permintaan ke server untuk memperbarui jumlah
        fetch(`/cart/update/${itemId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ jumlah: newQty })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                qtyElement.value = newQty;
                // Refresh halaman atau perbarui total
                location.reload();
            } else {
                alert('Gagal memperbarui jumlah item');
            }
        })
        .catch(error => console.error('Error:', error));
    }
</script>
@endsection