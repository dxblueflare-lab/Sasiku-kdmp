<?php
// shop.php
// Product listing page for KDMP application

require_once __DIR__ . '/includes/DatabaseConfig.php';
require_once __DIR__ . '/includes/DatabaseStorage.php';
require_once __DIR__ . '/includes/DatabaseCRUD.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION['user'] ?? null;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko - Dapur Suplai</title>
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
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(220, 38, 38, 0.15);
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
                        <h1 class="text-xl font-normal text-merah tracking-tight">DAPUR SUPLAI</h1>
                        <p class="text-xs text-gray-600 font-medium">Toko Produk</p>
                    </div>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="index.php" class="text-gray-700 hover:text-merah font-semibold transition">Beranda</a>
                    <a href="shop.php" class="text-merah font-semibold transition">Belanja</a>
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
                        <a href="shop.php" class="text-merah font-semibold transition py-2">Belanja</a>
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

    <!-- Main Content -->
    <main class="pt-24 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-800 mb-4">Katalog Produk</h1>
                <p class="text-gray-600">Temukan bahan pangan berkualitas untuk kebutuhan dapur Anda</p>
            </div>

            <!-- Category Filter -->
            <div class="flex flex-wrap justify-center gap-4 mb-12">
                <button onclick="filterProducts('all')" class="category-btn active bg-merah text-white px-6 py-2 rounded-full font-semibold transition shadow-lg" data-category="all">
                    Semua
                </button>
                <?php 
                try {
                    $crud = new DatabaseCRUD();
                    $categories = $crud->read('kategori_produk', [], '*', 'id ASC');
                    
                    if ($categories['success'] && !empty($categories['data'])) {
                        foreach ($categories['data'] as $category):
                ?>
                <button onclick="filterProducts('<?php echo strtolower($category['nama_kategori']); ?>')" class="category-btn bg-gray-200 text-gray-700 px-6 py-2 rounded-full font-semibold hover:bg-gray-300 transition" data-category="<?php echo strtolower($category['nama_kategori']); ?>">
                    <?php echo htmlspecialchars($category['nama_kategori']); ?>
                </button>
                <?php 
                        endforeach;
                    }
                } catch (Exception $e) {
                    // Handle error silently or show a message
                }
                ?>
            </div>

            <!-- Search Bar -->
            <div class="max-w-2xl mx-auto mb-12">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input 
                        type="text" 
                        id="search-input" 
                        placeholder="Cari produk..." 
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-full focus:ring-2 focus:ring-merah focus:border-transparent outline-none transition"
                        onkeyup="searchProducts()"
                    >
                </div>
            </div>

            <!-- Products Grid -->
            <div id="products-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php 
                try {
                    $products = $crud->read('produk', [], '*', 'id DESC');
                    
                    if ($products['success'] && !empty($products['data'])) {
                        foreach ($products['data'] as $product):
                            // Get category name
                            $category = $crud->findById('kategori_produk', $product['id_kategori']);
                            $category_name = $category['success'] ? $category['data']['nama_kategori'] : 'Umum';
                            
                            // Get supplier name
                            $supplier = $crud->findById('users', $product['id_supplier']);
                            $supplier_name = $supplier['success'] ? ($supplier['data']['nama_lengkap'] ?? $supplier['data']['username']) : 'Unknown';
                ?>
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden card-hover" data-category="<?php echo strtolower($category_name); ?>" data-name="<?php echo strtolower(htmlspecialchars($product['nama_produk'])); ?>">
                    <div class="relative">
                        <img src="<?php echo $product['gambar_produk'] ? htmlspecialchars($product['gambar_produk']) : 'https://placehold.co/400x300?text=No+Image'; ?>" alt="<?php echo htmlspecialchars($product['nama_produk']); ?>" class="w-full h-48 object-cover">
                        <div class="absolute top-3 right-3 bg-merah text-white text-xs px-2 py-1 rounded-full">
                            Stok: <?php echo $product['stok']; ?>
                        </div>
                    </div>
                    <div class="p-6">
                        <span class="text-xs text-gray-500"><?php echo htmlspecialchars($category_name); ?></span>
                        <h3 class="font-bold text-lg text-gray-800 mb-2"><?php echo htmlspecialchars($product['nama_produk']); ?></h3>
                        <p class="text-gray-600 text-sm mb-4"><?php echo htmlspecialchars(substr($product['deskripsi'], 0, 60)); ?><?php echo strlen($product['deskripsi']) > 60 ? '...' : ''; ?></p>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-merah font-bold">Rp <?php echo number_format($product['harga'], 0, ',', '.'); ?></span>
                            <span class="text-gray-500 text-sm">oleh <?php echo htmlspecialchars($supplier_name); ?></span>
                        </div>
                        <button class="w-full bg-merah text-white py-2 rounded-full hover:bg-red-700 transition text-sm" onclick="addToCart(<?php echo $product['id']; ?>)">
                            <i class="fas fa-shopping-cart mr-1"></i> Tambah ke Keranjang
                        </button>
                    </div>
                </div>
                <?php 
                        endforeach;
                    } else {
                ?>
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-700">Tidak ada produk ditemukan</h3>
                    <p class="text-gray-500 mt-2">Silakan coba kategori atau pencarian lain</p>
                </div>
                <?php 
                    }
                } catch (Exception $e) {
                ?>
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-exclamation-triangle text-6xl text-red-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-red-700">Terjadi kesalahan</h3>
                    <p class="text-red-500 mt-2">Gagal memuat produk. Silakan coba lagi nanti.</p>
                </div>
                <?php 
                }
                ?>
            </div>
        </div>
    </main>

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
    <footer class="bg-gelap text-white py-8">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p>&copy; 2024 Dapur Suplai. Hak Cipta Dilindungi.</p>
        </div>
    </footer>

    <script>
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

        // Fungsi untuk pencarian produk
        function searchProducts() {
            const searchTerm = document.getElementById('search-input').value.toLowerCase();
            const products = document.querySelectorAll('[data-name]');
            
            products.forEach(product => {
                const productName = product.getAttribute('data-name');
                if (productName.includes(searchTerm)) {
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