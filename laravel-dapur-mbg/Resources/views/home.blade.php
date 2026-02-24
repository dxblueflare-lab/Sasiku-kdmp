@extends('layouts.app')

@section('title', 'Beranda - Dapur Suplai')

@section('content')
<!-- Hero Section -->
<section id="beranda" class="gradient-hero pt-32 pb-20 px-4">
    <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-12 items-center">
        <div class="text-white space-y-6 animate-slide-up">
            <div class="inline-block bg-white/20 backdrop-blur px-4 py-2 rounded-full text-sm font-semibold mb-4">
                <i class="fas fa-check-circle mr-2"></i> Sistem Terintegrasi SPPG
            </div>
            <h2 class="text-5xl md:text-6xl font-bold leading-tight">
                Solusi Pangan <br>
                <span class="text-emas">Berkualitas</span> untuk Desa
            </h2>
            <p class="text-xl text-red-100 leading-relaxed">
                Platform digital koperasi desa/kota untuk pengadaan bahan pangan dapur SPPG MBg dengan sistem monitoring real-time dan pembayaran terintegrasi.
            </p>
            <div class="flex flex-wrap gap-4 pt-4">
                <a href="{{ url('/products') }}" class="bg-white text-merah px-8 py-4 rounded-full font-bold text-lg hover:bg-gray-100 transition shadow-xl flex items-center space-x-2">
                    <span>Mulai Belanja</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
                <a href="#monitoring" class="bg-transparent border-2 border-white text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-white/10 transition">
                    Cek Pesanan
                </a>
            </div>

            <div class="flex items-center space-x-8 pt-8 text-sm">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-shield-alt text-emas text-2xl"></i>
                    <span>Transaksi Aman</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-clock text-emas text-2xl"></i>
                    <span>Real-time Update</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-award text-emas text-2xl"></i>
                    <span>Bahan Berkualitas</span>
                </div>
            </div>
        </div>

        <div class="relative animate-float hidden md:block">
            <div class="absolute inset-0 bg-white/10 rounded-full blur-3xl"></div>
            <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?w=800&h=600&fit=crop" alt="Fresh vegetables" class="relative rounded-3xl shadow-2xl border-4 border-white/20 w-full object-cover h-[500px]">

            <!-- Floating Cards -->
            <div class="absolute -bottom-6 -left-6 bg-white p-4 rounded-2xl shadow-xl animate-slide-up" style="animation-delay: 0.2s">
                <div class="flex items-center space-x-3">
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-leaf text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Produk Segar</p>
                        <p class="font-bold text-gray-800">100% Organik</p>
                    </div>
                </div>
            </div>

            <div class="absolute -top-6 -right-6 bg-white p-4 rounded-2xl shadow-xl animate-slide-up" style="animation-delay: 0.4s">
                <div class="flex items-center space-x-3">
                    <div class="bg-red-100 p-3 rounded-full">
                        <i class="fas fa-fire text-merah text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Pengiriman</p>
                        <p class="font-bold text-gray-800">Same Day</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-12 bg-white -mt-10 relative z-10 mx-4 rounded-3xl shadow-xl max-w-6xl mx-auto">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
        <div class="space-y-2">
            <p class="text-4xl font-bold text-merah">500+</p>
            <p class="text-gray-600 text-sm">Produk Tersedia</p>
        </div>
        <div class="space-y-2">
            <p class="text-4xl font-bold text-merah">50+</p>
            <p class="text-gray-600 text-sm">Desa Mitra</p>
        </div>
        <div class="space-y-2">
            <p class="text-4xl font-bold text-merah">24/7</p>
            <p class="text-gray-600 text-sm">Layanan Monitoring</p>
        </div>
        <div class="space-y-2">
            <p class="text-4xl font-bold text-merah">100%</p>
            <p class="text-gray-600 text-sm">Transaksi Aman</p>
        </div>
    </div>
</section>

<!-- Shopping Section -->
<section id="belanja" class="py-20 px-4 max-w-7xl mx-auto">
    <div class="text-center mb-12">
        <h3 class="text-merah font-bold text-lg mb-2">KATALOG PRODUK</h3>
        <h2 class="text-4xl font-bold text-gray-800 mb-4">Pilihan Bahan Pangan SPPG</h2>
        <div class="w-24 h-1 bg-merah mx-auto rounded-full"></div>
    </div>

    <!-- Category Filter -->
    <div class="flex flex-wrap justify-center gap-4 mb-12">
        <button onclick="filterProducts('all')" class="category-btn active bg-merah text-white px-6 py-2 rounded-full font-semibold transition shadow-lg" data-category="all">
            Semua
        </button>
        <button onclick="filterProducts('sayur')" class="category-btn bg-gray-200 text-gray-700 px-6 py-2 rounded-full font-semibold hover:bg-gray-300 transition" data-category="sayur">
            Sayur Segar
        </button>
        <button onclick="filterProducts('protein')" class="category-btn bg-gray-200 text-gray-700 px-6 py-2 rounded-full font-semibold hover:bg-gray-300 transition" data-category="protein">
            Protein Hewani
        </button>
        <button onclick="filterProducts('sembako')" class="category-btn bg-gray-200 text-gray-700 px-6 py-2 rounded-full font-semibold hover:bg-gray-300 transition" data-category="sembako">
            Sembako
        </button>
        <button onclick="filterProducts('buah')" class="category-btn bg-gray-200 text-gray-700 px-6 py-2 rounded-full font-semibold hover:bg-gray-300 transition" data-category="buah">
            Buah-buahan
        </button>
    </div>

    <!-- Products Grid -->
    <div id="products-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($produk as $item)
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden card-hover">
            <img src="{{ $item->gambar_produk ? asset('storage/' . $item->gambar_produk) : 'https://placehold.co/400x300?text=No+Image' }}" alt="{{ $item->nama_produk }}" class="w-full h-48 object-cover">
            <div class="p-6">
                <h3 class="font-bold text-lg text-gray-800 mb-2">{{ $item->nama_produk }}</h3>
                <p class="text-gray-600 text-sm mb-4">{{ Str::limit($item->deskripsi, 60) }}</p>
                <div class="flex justify-between items-center">
                    <span class="text-merah font-bold">Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                    <button class="bg-merah text-white px-4 py-2 rounded-full hover:bg-red-700 transition text-sm" onclick="addToCart({{ $item->id }})">
                        <i class="fas fa-shopping-cart mr-1"></i> Beli
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <div class="mt-8">
        {{ $produk->links() }}
    </div>
</section>

<!-- Payment Flow Section -->
<section id="pembayaran" class="py-20 bg-gray-100">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-16">
            <h3 class="text-merah font-bold text-lg mb-2">ALUR PEMBAYARAN</h3>
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Cara Pembayaran yang Mudah</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Sistem pembayaran terintegrasi dengan multiple payment gateway untuk kemudahan transaksi koperasi desa</p>
        </div>

        <div class="grid md:grid-cols-4 gap-8">
            <!-- Step 1 -->
            <div class="relative">
                <div class="bg-white rounded-2xl p-8 shadow-lg card-hover text-center relative z-10">
                    <div class="w-16 h-16 bg-merah rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4 shadow-lg">1</div>
                    <h4 class="font-bold text-xl mb-2">Pilih Produk</h4>
                    <p class="text-gray-600 text-sm">Tambahkan bahan pangan ke keranjang belanja sesuai kebutuhan dapur SPPG</p>
                    <i class="fas fa-shopping-basket text-4xl text-red-200 mt-4"></i>
                </div>
                <div class="hidden md:block absolute top-1/2 -right-4 text-merah text-2xl z-0">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="relative">
                <div class="bg-white rounded-2xl p-8 shadow-lg card-hover text-center relative z-10">
                    <div class="w-16 h-16 bg-merah rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4 shadow-lg">2</div>
                    <h4 class="font-bold text-xl mb-2">Verifikasi</h4>
                    <p class="text-gray-600 text-sm">Admin koperasi memverifikasi ketersediaan stok dan harga terkini</p>
                    <i class="fas fa-clipboard-check text-4xl text-red-200 mt-4"></i>
                </div>
                <div class="hidden md:block absolute top-1/2 -right-4 text-merah text-2xl z-0">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="relative">
                <div class="bg-white rounded-2xl p-8 shadow-lg card-hover text-center relative z-10">
                    <div class="w-16 h-16 bg-merah rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4 shadow-lg">3</div>
                    <h4 class="font-bold text-xl mb-2">Pembayaran</h4>
                    <p class="text-gray-600 text-sm">Bayar via transfer bank, e-wallet, atau tunai di koperasi</p>
                    <div class="flex justify-center space-x-2 mt-4 text-2xl text-gray-400">
                        <i class="fas fa-money-bill-wave"></i>
                        <i class="fas fa-mobile-alt"></i>
                        <i class="fas fa-credit-card"></i>
                    </div>
                </div>
                <div class="hidden md:block absolute top-1/2 -right-4 text-merah text-2xl z-0">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </div>

            <!-- Step 4 -->
            <div class="relative">
                <div class="bg-white rounded-2xl p-8 shadow-lg card-hover text-center relative z-10">
                    <div class="w-16 h-16 bg-merah rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4 shadow-lg">4</div>
                    <h4 class="font-bold text-xl mb-2">Pengiriman</h4>
                    <p class="text-gray-600 text-sm">Pesanan diproses dan dikirim ke lokasi dapur SPPG dengan tracking</p>
                    <i class="fas fa-truck text-4xl text-red-200 mt-4"></i>
                </div>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="mt-16 bg-white rounded-3xl p-8 shadow-lg">
            <h4 class="text-center font-bold text-2xl mb-8">Metode Pembayaran yang Tersedia</h4>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
                <div class="border-2 border-gray-200 rounded-xl p-4 flex flex-col items-center hover:border-merah transition cursor-pointer group">
                    <i class="fas fa-university text-4xl text-blue-600 mb-2 group-hover:scale-110 transition"></i>
                    <span class="font-semibold text-sm">Transfer Bank</span>
                </div>
                <div class="border-2 border-gray-200 rounded-xl p-4 flex flex-col items-center hover:border-merah transition cursor-pointer group">
                    <i class="fas fa-wallet text-4xl text-purple-600 mb-2 group-hover:scale-110 transition"></i>
                    <span class="font-semibold text-sm">E-Wallet</span>
                </div>
                <div class="border-2 border-gray-200 rounded-xl p-4 flex flex-col items-center hover:border-merah transition cursor-pointer group">
                    <i class="fas fa-money-bill-wave text-4xl text-green-600 mb-2 group-hover:scale-110 transition"></i>
                    <span class="font-semibold text-sm">Tunai</span>
                </div>
                <div class="border-2 border-gray-200 rounded-xl p-4 flex flex-col items-center hover:border-merah transition cursor-pointer group">
                    <i class="fas fa-qrcode text-4xl text-gray-800 mb-2 group-hover:scale-110 transition"></i>
                    <span class="font-semibold text-sm">QRIS</span>
                </div>
                <div class="border-2 border-gray-200 rounded-xl p-4 flex flex-col items-center hover:border-merah transition cursor-pointer group">
                    <i class="fas fa-file-invoice text-4xl text-orange-600 mb-2 group-hover:scale-110 transition"></i>
                    <span class="font-semibold text-sm">Invoice</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Monitoring Section -->
<section id="monitoring" class="py-20 px-4 max-w-7xl mx-auto">
    <div class="text-center mb-12">
        <h3 class="text-merah font-bold text-lg mb-2">MONITORING PESANAN</h3>
        <h2 class="text-4xl font-bold text-gray-800 mb-4">Lacak Status Pemesanan Bahan</h2>
        <p class="text-gray-600 max-w-2xl mx-auto">Pantau real-time status pengadaan bahan pangan untuk dapur SPPG MBg</p>
    </div>

    <!-- Search Tracking -->
    <div class="max-w-2xl mx-auto mb-12">
        <div class="flex shadow-lg rounded-full overflow-hidden">
            <input type="text" placeholder="Masukkan nomor pesanan (contoh: SPPG-2024-001)" class="flex-1 px-6 py-4 outline-none text-gray-700">
            <button class="bg-merah text-white px-8 py-4 font-semibold hover:bg-red-700 transition flex items-center space-x-2">
                <i class="fas fa-search"></i>
                <span>Lacak</span>
            </button>
        </div>
    </div>

    <!-- Active Orders -->
    <div class="space-y-6" id="orders-container">
        <!-- Orders will be inserted here by JavaScript -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-merah">
            <div class="flex justify-between items-start">
                <div>
                    <h4 class="font-bold text-lg text-gray-800">SPPG-2024-001</h4>
                    <p class="text-gray-600 text-sm">Dipesan pada 15 Januari 2024</p>
                </div>
                <div class="text-right">
                    <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-semibold status-pill">Diproses</span>
                    <p class="text-gray-800 font-bold mt-2">Rp 125.000</p>
                </div>
            </div>
            <div class="mt-4">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-merah h-2 rounded-full" style="width: 50%"></div>
                </div>
                <div class="flex justify-between text-xs text-gray-500 mt-2">
                    <span>Pesanan Diterima</span>
                    <span>Diproses</span>
                    <span>Dikirim</span>
                    <span>Diterima</span>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex justify-between items-start">
                <div>
                    <h4 class="font-bold text-lg text-gray-800">SPPG-2024-002</h4>
                    <p class="text-gray-600 text-sm">Dipesan pada 18 Januari 2024</p>
                </div>
                <div class="text-right">
                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">Selesai</span>
                    <p class="text-gray-800 font-bold mt-2">Rp 87.500</p>
                </div>
            </div>
            <div class="mt-4">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-500 h-2 rounded-full" style="width: 100%"></div>
                </div>
                <div class="flex justify-between text-xs text-gray-500 mt-2">
                    <span>Pesanan Diterima</span>
                    <span>Diproses</span>
                    <span>Dikirim</span>
                    <span>Diterima</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Monitoring Dashboard -->
    <div class="mt-16 bg-gradient-to-br from-gray-900 to-gray-800 rounded-3xl p-8 text-white">
        <div class="flex flex-col md:flex-row justify-between items-center mb-8">
            <div>
                <h3 class="text-2xl font-bold mb-2">Dashboard Monitoring</h3>
                <p class="text-gray-400">Ringkasan status pengadaan bulan ini</p>
            </div>
            <button class="mt-4 md:mt-0 bg-emas text-gray-900 px-6 py-2 rounded-full font-bold hover:bg-yellow-400 transition">
                <i class="fas fa-download mr-2"></i>Download Laporan
            </button>
        </div>

        <div class="grid md:grid-cols-4 gap-6">
            <div class="bg-white/10 backdrop-blur rounded-2xl p-6 border border-white/10">
                <div class="flex justify-between items-start mb-4">
                    <div class="bg-green-500/20 p-3 rounded-lg">
                        <i class="fas fa-check-circle text-green-400 text-xl"></i>
                    </div>
                    <span class="text-green-400 text-sm font-semibold">+12%</span>
                </div>
                <p class="text-3xl font-bold mb-1">24</p>
                <p class="text-gray-400 text-sm">Pesanan Selesai</p>
            </div>

            <div class="bg-white/10 backdrop-blur rounded-2xl p-6 border border-white/10">
                <div class="flex justify-between items-start mb-4">
                    <div class="bg-yellow-500/20 p-3 rounded-lg">
                        <i class="fas fa-clock text-yellow-400 text-xl"></i>
                    </div>
                    <span class="text-yellow-400 text-sm font-semibold">5 Aktif</span>
                </div>
                <p class="text-3xl font-bold mb-1">5</p>
                <p class="text-gray-400 text-sm">Dalam Proses</p>
            </div>

            <div class="bg-white/10 backdrop-blur rounded-2xl p-6 border border-white/10">
                <div class="flex justify-between items-start mb-4">
                    <div class="bg-blue-500/20 p-3 rounded-lg">
                        <i class="fas fa-truck text-blue-400 text-xl"></i>
                    </div>
                    <span class="text-blue-400 text-sm font-semibold">3 Hari</span>
                </div>
                <p class="text-3xl font-bold mb-1">8</p>
                <p class="text-gray-400 text-sm">Dalam Pengiriman</p>
            </div>

            <div class="bg-white/10 backdrop-blur rounded-2xl p-6 border border-white/10">
                <div class="flex justify-between items-start mb-4">
                    <div class="bg-red-500/20 p-3 rounded-lg">
                        <i class="fas fa-exclamation-triangle text-red-400 text-xl"></i>
                    </div>
                    <span class="text-red-400 text-sm font-semibold">Perlu Atensi</span>
                </div>
                <p class="text-3xl font-bold mb-1">2</p>
                <p class="text-gray-400 text-sm">Pending Verifikasi</p>
            </div>
        </div>
    </div>
</section>

<script>
    // Fungsi untuk filter produk
    function filterProducts(category) {
        // Hapus kelas 'active' dari semua tombol
        document.querySelectorAll('.category-btn').forEach(btn => {
            btn.classList.remove('active', 'bg-merah', 'text-white');
            btn.classList.add('bg-gray-200', 'text-gray-700');
        });
        
        // Tambahkan kelas 'active' ke tombol yang diklik
        event.target.classList.add('active', 'bg-merah', 'text-white');
        event.target.classList.remove('bg-gray-200', 'text-gray-700');
        
        // Diimplementasikan lebih lanjut saat sistem produk dibuat
        console.log(`Filter produk berdasarkan kategori: ${category}`);
    }
</script>
@endsection