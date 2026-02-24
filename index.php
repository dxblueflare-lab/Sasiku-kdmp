<?php
// index.php
// Main landing page for KDMP application with database integration

require_once __DIR__ . '/includes/DatabaseConfig.php';
require_once __DIR__ . '/includes/DatabaseStorage.php';
require_once __DIR__ . '/includes/DatabaseCRUD.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get user info if logged in
$user = $_SESSION['user'] ?? null;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koperasi Merah Putih - Dapur SPPG MBG</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        merah: '#DC2626',
                        putih: '#FFFFFF',
                        emas: '#F59E0B',
                        gelap: '#1F2937',
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'slide-up': 'slideUp 0.5s ease-out',
                        'fade-in': 'fadeIn 0.3s ease-out',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .gradient-hero {
            background: linear-gradient(135deg, #DC2626 0%, #991B1B 50%, #7F1D1D 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(220, 38, 38, 0.15);
        }
        .status-pill {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .7; }
        }
        .progress-bar {
            transition: width 1s ease-in-out;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">

    <!-- Navigation -->
    <nav class="fixed w-full z-50 glass-effect shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center space-x-3">
                    <div class="w-24 h-24 rounded-full flex items-center justify-center">
                         <img src="https://www.appdapursuplai.org/images/logo.png" alt="Logo" width="400" height="400">
                    </div>
                    <div>
                        <h1 class="text-2xl font-normal text-merah tracking-tight">DAPUR SUPLAI</h1>
                        <p class="text-xs text-gray-600 font-medium">Supporting Koperasi Desa & Kelurahan Merah Putih</p>
                    </div>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="index.php" class="text-gray-700 hover:text-merah font-semibold transition">Beranda</a>
                    <a href="shop.php" class="text-gray-700 hover:text-merah font-semibold transition">Belanja</a>
                    <a href="#monitoring" class="text-gray-700 hover:text-merah font-semibold transition">Monitoring</a>
                    <a href="customer/cart.php" class="text-gray-700 hover:text-merah font-semibold transition relative">
                        Keranjang
                        <span id="cart-badge" class="absolute -top-2 -right-3 bg-merah text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
                    </a>

                    <?php if ($user): ?>
                    <div class="relative">
                        <a href="dashboard/<?php echo $user['role']; ?>/index.php" class="bg-emerald-500 text-white px-6 py-2 rounded-full hover:bg-emerald-600 transition shadow-lg flex items-center space-x-2">
                            <i class="fas fa-user"></i>
                            <span><?php echo htmlspecialchars($user['nama_lengkap'] ?? $user['username']); ?></span>
                        </a>
                    </div>
                    <a href="auth/logout.php" class="text-gray-700 hover:text-merah font-semibold transition ml-4">Logout</a>
                    <?php else: ?>
                    <div class="relative">
                        <a href="auth/login.php" class="bg-emerald-500 text-white px-6 py-2 rounded-full hover:bg-emerald-600 transition shadow-lg flex items-center space-x-2">
                            <i class="fas fa-user"></i>
                            <span>Login</span>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Mobile Menu -->
                <div class="mobile-menu hidden absolute top-20 left-0 right-0 bg-white shadow-xl z-50 md:hidden">
                    <div class="flex flex-col py-4 space-y-4 px-6">
                        <a href="index.php" class="text-gray-700 hover:text-merah font-semibold transition py-2">Beranda</a>
                        <a href="shop.php" class="text-gray-700 hover:text-merah font-semibold transition py-2">Belanja</a>
                        <a href="#monitoring" class="text-gray-700 hover:text-merah font-semibold transition py-2">Monitoring</a>
                        <a href="customer/cart.php" class="text-gray-700 hover:text-merah font-semibold transition py-2 relative">
                            Keranjang
                            <span id="cart-badge-mobile" class="absolute -top-1 -right-6 bg-merah text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
                        </a>
                        
                        <?php if ($user): ?>
                        <a href="dashboard/<?php echo $user['role']; ?>/index.php" class="bg-emerald-500 text-white px-6 py-2 rounded-full hover:bg-emerald-600 transition shadow-lg flex items-center space-x-2 w-fit">
                            <i class="fas fa-user"></i>
                            <span><?php echo htmlspecialchars($user['nama_lengkap'] ?? $user['username']); ?></span>
                        </a>
                        <a href="auth/logout.php" class="text-gray-700 hover:text-merah font-semibold transition py-2">Logout</a>
                        <?php else: ?>
                        <a href="auth/login.php" class="bg-emerald-500 text-white px-6 py-2 rounded-full hover:bg-emerald-600 transition shadow-lg flex items-center space-x-2 w-fit">
                            <i class="fas fa-user"></i>
                            <span>Login</span>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

                <button class="md:hidden text-merah text-2xl" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </nav>

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
                    <button onclick="scrollToSection('belanja')" class="bg-white text-merah px-8 py-4 rounded-full font-bold hover:bg-gray-100 transition shadow-xl flex items-center space-x-2">
                        <span>Mulai Belanja</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                    <button onclick="scrollToSection('monitoring')" class="bg-transparent border-2 border-white text-white px-8 py-4 rounded-full font-bold hover:bg-white/10 transition">
                        Cek Pesanan
                    </button>
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
                <p class="text-4xl font-bold text-merah"><?php echo get_product_count(); ?></p>
                <p class="text-gray-600 text-sm">Produk Tersedia</p>
            </div>
            <div class="space-y-2">
                <p class="text-4xl font-bold text-merah"><?php echo get_supplier_count(); ?></p>
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
            <?php 
            $categories = get_categories();
            foreach ($categories as $category):
            ?>
            <button onclick="filterProducts('<?php echo strtolower($category['nama_kategori']); ?>')" class="category-btn bg-gray-200 text-gray-700 px-6 py-2 rounded-full font-semibold hover:bg-gray-300 transition" data-category="<?php echo strtolower($category['nama_kategori']); ?>">
                <?php echo htmlspecialchars($category['nama_kategori']); ?>
            </button>
            <?php endforeach; ?>
        </div>

        <!-- Products Grid -->
        <div id="products-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php 
            $products = get_products_with_limit(8);
            foreach ($products as $product):
            ?>
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden card-hover" data-category="<?php echo strtolower(get_category_name_by_id($product['id_kategori'])); ?>">
                <img src="<?php echo $product['gambar_produk'] ? htmlspecialchars($product['gambar_produk']) : 'https://placehold.co/400x300?text=No+Image'; ?>" alt="<?php echo htmlspecialchars($product['nama_produk']); ?>" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h3 class="font-bold text-lg text-gray-800 mb-2"><?php echo htmlspecialchars($product['nama_produk']); ?></h3>
                    <p class="text-gray-600 text-sm mb-4"><?php echo htmlspecialchars(substr($product['deskripsi'], 0, 60)); ?><?php echo strlen($product['deskripsi']) > 60 ? '...' : ''; ?></p>
                    <div class="flex justify-between items-center">
                        <span class="text-merah font-bold">Rp <?php echo number_format($product['harga'], 0, ',', '.'); ?></span>
                        <button class="bg-merah text-white px-4 py-2 rounded-full hover:bg-red-700 transition text-sm" onclick="addToCart(<?php echo $product['id']; ?>)">
                            <i class="fas fa-shopping-cart mr-1"></i> Beli
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
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
                <div>
                    <div class="bg-white rounded-2xl p-8 shadow-lg card-hover text-center">
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
            <?php if ($user): ?>
            <?php 
            $orders = get_orders_by_user($user['id']);
            foreach ($orders as $order):
            ?>
            <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 <?php echo get_order_status_color($order['status_pesanan']); ?>">
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="font-bold text-lg text-gray-800">#<?php echo htmlspecialchars($order['id']); ?></h4>
                        <p class="text-gray-600 text-sm">Dipesan pada <?php echo date('d M Y', strtotime($order['tanggal_pesan'])); ?></p>
                    </div>
                    <div class="text-right">
                        <span class="px-3 py-1 rounded-full text-sm font-semibold <?php echo get_order_status_class($order['status_pesanan']); ?>"><?php echo ucfirst(str_replace('_', ' ', $order['status_pesanan'])); ?></span>
                        <p class="text-gray-800 font-bold mt-2">Rp <?php echo number_format($order['total_harga'], 0, ',', '.'); ?></p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full <?php echo get_order_progress_color($order['status_pesanan']); ?>" style="width: <?php echo get_order_progress_percentage($order['status_pesanan']); ?>%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 mt-2">
                        <span>Pesanan Diterima</span>
                        <span>Diproses</span>
                        <span>Dikirim</span>
                        <span>Diterima</span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php else: ?>
            <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-merah">
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="font-bold text-lg text-gray-800">Silakan Login</h4>
                        <p class="text-gray-600 text-sm">Untuk melihat pesanan Anda</p>
                    </div>
                    <div class="text-right">
                        <a href="auth/login.php" class="bg-merah text-white px-4 py-2 rounded-full hover:bg-red-700 transition text-sm">Login</a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
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
                    <p class="text-3xl font-bold mb-1"><?php echo get_completed_orders_count(); ?></p>
                    <p class="text-gray-400 text-sm">Pesanan Selesai</p>
                </div>

                <div class="bg-white/10 backdrop-blur rounded-2xl p-6 border border-white/10">
                    <div class="flex justify-between items-start mb-4">
                        <div class="bg-yellow-500/20 p-3 rounded-lg">
                            <i class="fas fa-clock text-yellow-400 text-xl"></i>
                        </div>
                        <span class="text-yellow-400 text-sm font-semibold"><?php echo get_active_orders_count(); ?> Aktif</span>
                    </div>
                    <p class="text-3xl font-bold mb-1"><?php echo get_active_orders_count(); ?></p>
                    <p class="text-gray-400 text-sm">Dalam Proses</p>
                </div>

                <div class="bg-white/10 backdrop-blur rounded-2xl p-6 border border-white/10">
                    <div class="flex justify-between items-start mb-4">
                        <div class="bg-blue-500/20 p-3 rounded-lg">
                            <i class="fas fa-truck text-blue-400 text-xl"></i>
                        </div>
                        <span class="text-blue-400 text-sm font-semibold">3 Hari</span>
                    </div>
                    <p class="text-3xl font-bold mb-1"><?php echo get_shipped_orders_count(); ?></p>
                    <p class="text-gray-400 text-sm">Dalam Pengiriman</p>
                </div>

                <div class="bg-white/10 backdrop-blur rounded-2xl p-6 border border-white/10">
                    <div class="flex justify-between items-start mb-4">
                        <div class="bg-red-500/20 p-3 rounded-lg">
                            <i class="fas fa-exclamation-triangle text-red-400 text-xl"></i>
                        </div>
                        <span class="text-red-400 text-sm font-semibold">Perlu Atensi</span>
                    </div>
                    <p class="text-3xl font-bold mb-1"><?php echo get_pending_orders_count(); ?></p>
                    <p class="text-gray-400 text-sm">Pending Verifikasi</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Cart Modal -->
    <div id="cart-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="toggleCart()"></div>
        <div class="absolute right-0 top-0 h-full w-full max-w-md bg-white shadow-2xl transform transition-transform duration-300 translate-x-full" id="cart-panel">
            <div class="flex flex-col h-full">
                <div class="p-6 border-b flex justify-between items-center bg-merah text-white">
                    <h3 class="text-xl font-bold"><i class="fas fa-shopping-cart mr-2"></i>Keranjang Belanja</h3>
                    <button onclick="toggleCart()" class="hover:bg-white/20 p-2 rounded-full transition">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-6" id="cart-items">
                    <div class="text-center text-gray-500 mt-12">
                        <i class="fas fa-shopping-basket text-6xl mb-4 text-gray-300"></i>
                        <p>Keranjang masih kosong</p>
                        <button onclick="toggleCart(); scrollToSection('belanja')" class="mt-4 text-merah font-semibold hover:underline">Mulai Belanja</button>
                    </div>
                </div>

                <div class="border-t p-6 bg-gray-50">
                    <div class="flex justify-between mb-4 text-lg font-bold">
                        <span>Total:</span>
                        <span id="cart-total" class="text-merah">Rp 0</span>
                    </div>
                    <button onclick="checkout()" class="w-full bg-merah text-white py-4 rounded-xl font-bold hover:bg-red-700 transition shadow-lg flex items-center justify-center space-x-2">
                        <span>Proses Pembayaran</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gelap text-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-emas rounded-full flex items-center justify-center">
                            <i class="fas fa-store text-gelap"></i>
                        </div>
                        <h4 class="text-xl font-bold">DAPUR SUPLAI</h4>
                    </div>
                    <p class="text-gray-400 text-sm">
                        Platform digital koperasi desa/kota untuk pengadaan bahan pangan dapur SPPG MBg dengan sistem monitoring real-time dan pembayaran terintegrasi.
                    </p>
                </div>

                <div>
                    <h5 class="font-bold text-lg mb-4">Tautan</h5>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="index.php" class="hover:text-emas transition">Beranda</a></li>
                        <li><a href="shop.php" class="hover:text-emas transition">Belanja</a></li>
                        <li><a href="#monitoring" class="hover:text-emas transition">Monitoring</a></li>
                        <li><a href="customer/cart.php" class="hover:text-emas transition">Keranjang</a></li>
                        <?php if ($user): ?>
                        <li><a href="dashboard/<?php echo $user['role']; ?>/index.php" class="hover:text-emas transition">Dashboard</a></li>
                        <li><a href="auth/logout.php" class="hover:text-emas transition">Logout</a></li>
                        <?php else: ?>
                        <li><a href="auth/login.php" class="hover:text-emas transition">Login</a></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div>
                    <h5 class="font-bold text-lg mb-4">Kontak</h5>
                    <ul class="space-y-2 text-gray-400">
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Jl. Koperasi Desa No. 1</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-phone"></i>
                            <span>+62 812 3456 7890</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-envelope"></i>
                            <span>info@dapursppgmbg.co.id</span>
                        </li>
                    </ul>
                </div>

                <div>
                    <h5 class="font-bold text-lg mb-4">Jam Operasional</h5>
                    <ul class="space-y-2 text-gray-400">
                        <li>Senin - Jumat: 08:00 - 17:00</li>
                        <li>Sabtu: 08:00 - 15:00</li>
                        <li>Minggu: Libur</li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400 text-sm">
                <p>&copy; 2024 Dapur Suplai. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>

    <script>
        // Fungsi untuk menavigasi ke section tertentu
        function scrollToSection(sectionId) {
            const element = document.getElementById(sectionId);
            if (element) {
                element.scrollIntoView({ behavior: 'smooth' });

                // Tutup menu mobile jika sedang terbuka
                const mobileMenu = document.querySelector('.mobile-menu');
                if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.add('hidden');
                }
            } else {
                console.error(`Elemen dengan ID '${sectionId}' tidak ditemukan.`);
            }
        }

        // Fungsi untuk toggle keranjang
        function toggleCart() {
            const cartModal = document.getElementById('cart-modal');
            const cartPanel = document.getElementById('cart-panel');

            if (cartModal.classList.contains('hidden')) {
                // Update tampilan keranjang sebelum menampilkannya
                updateCartDisplay();

                cartModal.classList.remove('hidden');
                setTimeout(() => {
                    cartPanel.classList.remove('translate-x-full');
                }, 10);
            } else {
                cartPanel.classList.add('translate-x-full');
                setTimeout(() => {
                    cartModal.classList.add('hidden');
                }, 300);
            }
        }

        // Data keranjang sementara
        let cartItems = [];

        // Fungsi untuk menambahkan produk ke keranjang
        function addToCart(productId) {
            // Kirim permintaan ke server untuk mendapatkan informasi produk
            fetch('api/get_product.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id=' + productId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const productInfo = data.product;
                    
                    // Cek apakah produk sudah ada di keranjang
                    const existingItemIndex = cartItems.findIndex(item => item.id === productId);

                    if (existingItemIndex !== -1) {
                        // Jika sudah ada, tambahkan jumlahnya
                        cartItems[existingItemIndex].quantity += 1;
                    } else {
                        // Jika belum ada, tambahkan sebagai item baru
                        cartItems.push({
                            ...productInfo,
                            quantity: 1
                        });
                    }

                    // Update tampilan keranjang
                    updateCartDisplay();
                    updateCartBadge(cartItems.reduce((total, item) => total + item.quantity, 0));

                    // Tampilkan notifikasi
                    alert(`${productInfo.nama_produk} ditambahkan ke keranjang!`);
                } else {
                    alert('Gagal menambahkan produk ke keranjang: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menambahkan produk ke keranjang');
            });
        }

        // Fungsi untuk mengupdate tampilan keranjang
        function updateCartDisplay() {
            const cartItemsContainer = document.getElementById('cart-items');
            const cartTotalElement = document.getElementById('cart-total');

            if (cartItems.length === 0) {
                cartItemsContainer.innerHTML = `
                    <div class="text-center text-gray-500 mt-12">
                        <i class="fas fa-shopping-basket text-6xl mb-4 text-gray-300"></i>
                        <p>Keranjang masih kosong</p>
                        <button onclick="toggleCart(); scrollToSection('belanja')" class="mt-4 text-merah font-semibold hover:underline">Mulai Belanja</button>
                    </div>
                `;
                cartTotalElement.textContent = 'Rp 0';
                return;
            }

            let cartHTML = '';
            let total = 0;

            cartItems.forEach(item => {
                const itemTotal = item.harga * item.quantity;
                total += itemTotal;

                cartHTML += `
                    <div class="flex items-center justify-between py-4 border-b">
                        <div class="flex items-center space-x-4">
                            <img src="${item.gambar_produk || 'https://placehold.co/100x100?text=No+Image'}" alt="${item.nama_produk}" class="w-16 h-16 object-cover rounded-lg">
                            <div>
                                <h4 class="font-semibold">${item.nama_produk}</h4>
                                <p class="text-gray-600 text-sm">Rp ${item.harga.toLocaleString()}/${item.satuan || 'unit'}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center border rounded-lg">
                                <button onclick="updateQuantity(${item.id}, -1)" class="px-3 py-1 text-merah hover:bg-gray-100">-</button>
                                <span class="px-3 py-1">${item.quantity}</span>
                                <button onclick="updateQuantity(${item.id}, 1)" class="px-3 py-1 text-merah hover:bg-gray-100">+</button>
                            </div>
                            <div class="text-right">
                                <p class="font-bold">Rp ${itemTotal.toLocaleString()}</p>
                                <button onclick="removeFromCart(${item.id})" class="text-red-500 text-sm hover:text-red-700">Hapus</button>
                            </div>
                        </div>
                    </div>
                `;
            });

            cartItemsContainer.innerHTML = cartHTML;
            cartTotalElement.textContent = `Rp ${total.toLocaleString()}`;
        }

        // Fungsi untuk mengupdate jumlah produk di keranjang
        function updateQuantity(productId, change) {
            const itemIndex = cartItems.findIndex(item => item.id === productId);

            if (itemIndex !== -1) {
                cartItems[itemIndex].quantity += change;

                // Jika jumlah menjadi 0, hapus dari keranjang
                if (cartItems[itemIndex].quantity <= 0) {
                    cartItems.splice(itemIndex, 1);
                }

                updateCartDisplay();
                updateCartBadge(cartItems.reduce((total, item) => total + item.quantity, 0));
            }
        }

        // Fungsi untuk menghapus produk dari keranjang
        function removeFromCart(productId) {
            cartItems = cartItems.filter(item => item.id !== productId);
            updateCartDisplay();
            updateCartBadge(cartItems.reduce((total, item) => total + item.quantity, 0));
        }

        // Fungsi untuk checkout
        function checkout() {
            // Periksa apakah keranjang kosong
            if (cartItems.length === 0) {
                alert('Keranjang Anda masih kosong. Silakan pilih produk terlebih dahulu.');
                return;
            }

            // Simpan keranjang ke sessionStorage untuk digunakan di halaman checkout
            sessionStorage.setItem('cartItems', JSON.stringify(cartItems));
            
            // Redirect ke halaman checkout
            window.location.href = 'checkout.php';
        }

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

            // Filter produk berdasarkan kategori
            const products = document.querySelectorAll('[data-category]');
            products.forEach(product => {
                if (category === 'all' || product.getAttribute('data-category') === category) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });
        }

        // Fungsi untuk toggle menu mobile
        function toggleMobileMenu() {
            const mobileMenu = document.querySelector('.mobile-menu');
            if (mobileMenu) {
                mobileMenu.classList.toggle('hidden');
            }

            // Tutup modal keranjang jika sedang terbuka
            const cartModal = document.getElementById('cart-modal');
            const cartPanel = document.getElementById('cart-panel');
            if (!cartModal.classList.contains('hidden')) {
                cartPanel.classList.add('translate-x-full');
                setTimeout(() => {
                    cartModal.classList.add('hidden');
                }, 300);
            }
        }

        // Update badge keranjang
        function updateCartBadge(count) {
            const badge = document.getElementById('cart-badge');
            const mobileBadge = document.getElementById('cart-badge-mobile');

            if (count > 0) {
                badge.textContent = count;
                mobileBadge.textContent = count;
                badge.classList.remove('hidden');
                mobileBadge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
                mobileBadge.classList.add('hidden');
            }
        }
        
        // Load cart from session storage if available
        document.addEventListener('DOMContentLoaded', function() {
            const savedCart = sessionStorage.getItem('cartItems');
            if (savedCart) {
                cartItems = JSON.parse(savedCart);
                updateCartDisplay();
                updateCartBadge(cartItems.reduce((total, item) => total + item.quantity, 0));
            }
        });
    </script>
</body>
</html>

<?php
// Helper functions for database operations
function get_product_count() {
    try {
        $crud = new DatabaseCRUD();
        $result = $crud->count('produk');
        return $result['success'] ? $result['count'] : 0;
    } catch (Exception $e) {
        return 0;
    }
}

function get_supplier_count() {
    try {
        $crud = new DatabaseCRUD();
        $result = $crud->count('users', ['role' => 'supplier']);
        return $result['success'] ? $result['count'] : 0;
    } catch (Exception $e) {
        return 0;
    }
}

function get_categories() {
    try {
        $crud = new DatabaseCRUD();
        $result = $crud->read('kategori_produk', [], '*', 'id ASC');
        return $result['success'] ? $result['data'] : [];
    } catch (Exception $e) {
        return [];
    }
}

function get_products_with_limit($limit = 8) {
    try {
        $crud = new DatabaseCRUD();
        $result = $crud->read('produk', [], '*', 'id DESC', $limit);
        return $result['success'] ? $result['data'] : [];
    } catch (Exception $e) {
        return [];
    }
}

function get_category_name_by_id($id) {
    try {
        $crud = new DatabaseCRUD();
        $result = $crud->findById('kategori_produk', $id);
        return $result['success'] && $result['data'] ? $result['data']['nama_kategori'] : 'Umum';
    } catch (Exception $e) {
        return 'Umum';
    }
}

function get_orders_by_user($userId) {
    try {
        $crud = new DatabaseCRUD();
        $result = $crud->read('pesanan', ['id_user' => $userId], '*', 'tanggal_pesan DESC', 5);
        return $result['success'] ? $result['data'] : [];
    } catch (Exception $e) {
        return [];
    }
}

function get_order_status_color($status) {
    switch ($status) {
        case 'delivered':
            return 'border-green-500';
        case 'shipped':
            return 'border-blue-500';
        case 'processing':
        case 'confirmed':
            return 'border-yellow-500';
        case 'cancelled':
            return 'border-red-500';
        default:
            return 'border-merah';
    }
}

function get_order_status_class($status) {
    switch ($status) {
        case 'delivered':
            return 'bg-green-100 text-green-800';
        case 'shipped':
            return 'bg-blue-100 text-blue-800';
        case 'processing':
        case 'confirmed':
            return 'bg-yellow-100 text-yellow-800';
        case 'cancelled':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}

function get_order_progress_color($status) {
    switch ($status) {
        case 'delivered':
            return 'bg-green-500';
        case 'shipped':
            return 'bg-blue-500';
        case 'processing':
        case 'confirmed':
            return 'bg-yellow-500';
        case 'cancelled':
            return 'bg-red-500';
        default:
            return 'bg-merah';
    }
}

function get_order_progress_percentage($status) {
    switch ($status) {
        case 'delivered':
            return 100;
        case 'shipped':
            return 75;
        case 'processing':
        case 'confirmed':
            return 50;
        case 'pending':
            return 25;
        default:
            return 0;
    }
}

function get_completed_orders_count() {
    try {
        $crud = new DatabaseCRUD();
        $result = $crud->count('pesanan', ['status_pesanan' => 'delivered']);
        return $result['success'] ? $result['count'] : 0;
    } catch (Exception $e) {
        return 0;
    }
}

function get_active_orders_count() {
    try {
        $crud = new DatabaseCRUD();
        $result = $crud->count('pesanan', ['status_pesanan' => 'processing']);
        return $result['success'] ? $result['count'] : 0;
    } catch (Exception $e) {
        return 0;
    }
}

function get_shipped_orders_count() {
    try {
        $crud = new DatabaseCRUD();
        $result = $crud->count('pesanan', ['status_pesanan' => 'shipped']);
        return $result['success'] ? $result['count'] : 0;
    } catch (Exception $e) {
        return 0;
    }
}

function get_pending_orders_count() {
    try {
        $crud = new DatabaseCRUD();
        $result = $crud->count('pesanan', ['status_pesanan' => 'pending']);
        return $result['success'] ? $result['count'] : 0;
    } catch (Exception $e) {
        return 0;
    }
}
?>