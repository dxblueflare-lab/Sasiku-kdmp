<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dapur Suplai - Koperasi Merah Putih')</title>
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
    @stack('styles')
</head>
<body class="bg-gray-50 font-sans">
    <!-- Navigation -->
    <nav class="fixed w-full z-50 glass-effect shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-merah to-red-800 rounded-full flex items-center justify-center shadow-lg">
                         <img src="https://www.appdapursuplai.org/images/logo.png" alt="KDKMP" width="200" height="200">
                    </div>
                    <div>
                        <h1 class="text-2xl font-normal text-merah tracking-tight">DAPUR SUPLAI</h1>
                        <p class="text-xs text-gray-600 font-medium">Supporting Koperasi Desa & Kelurahan Merah Putih</p>
                    </div>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ url('/') }}" class="text-gray-700 hover:text-merah font-semibold transition">Beranda</a>
                    <a href="{{ url('/products') }}" class="text-gray-700 hover:text-merah font-semibold transition">Belanja</a>
                    <a href="#monitoring" class="text-gray-700 hover:text-merah font-semibold transition">Monitoring</a>
                    <a href="{{ route('cart') }}" class="text-gray-700 hover:text-merah font-semibold transition">Keranjang</a>

                    @auth
                        <div class="relative">
                            <a href="{{ route(Auth::user()->role.'.dashboard') }}" class="bg-emerald-500 text-white px-6 py-2 rounded-full hover:bg-emerald-600 transition shadow-lg flex items-center space-x-2">
                                <i class="fas fa-user"></i>
                                <span>{{ Auth::user()->nama_lengkap ?: Auth::user()->username }}</span>
                            </a>
                        </div>
                        
                        <div class="relative">
                            <a href="{{ route('logout') }}" class="bg-gray-500 text-white px-6 py-2 rounded-full hover:bg-gray-600 transition shadow-lg">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </a>
                        </div>
                    @else
                        <div class="relative">
                            <a href="{{ route('login') }}" class="bg-emerald-500 text-white px-6 py-2 rounded-full hover:bg-emerald-600 transition shadow-lg">
                                <i class="fas fa-user"></i>
                                <span>Login</span>
                            </a>
                        </div>
                    @endauth
                </div>

                <button class="md:hidden text-merah text-2xl" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="pt-20">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gelap text-white py-12 mt-12">
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
                        <li><a href="{{ url('/') }}" class="hover:text-emas transition">Beranda</a></li>
                        <li><a href="{{ url('/products') }}" class="hover:text-emas transition">Belanja</a></li>
                        <li><a href="#monitoring" class="hover:text-emas transition">Monitoring</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-emas transition">Login</a></li>
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
            document.getElementById(sectionId).scrollIntoView({ behavior: 'smooth' });
        }

        // Fungsi untuk toggle keranjang
        function toggleCart() {
            const cartModal = document.getElementById('cart-modal');
            const cartPanel = document.getElementById('cart-panel');
            
            if (cartModal.classList.contains('hidden')) {
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

        // Fungsi untuk menambahkan produk ke keranjang
        function addToCart(productId) {
            // Implementasi penambahan ke keranjang
            fetch(`/cart/add/${productId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    alert('Produk berhasil ditambahkan ke keranjang!');
                    updateCartCount();
                } else {
                    alert('Gagal menambahkan produk ke keranjang!');
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Fungsi untuk update jumlah item di keranjang
        function updateCartCount() {
            fetch('/cart/count')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('cart-badge');
                    if(data.count > 0) {
                        badge.textContent = data.count;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                });
        }

        // Panggil fungsi updateCartCount saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
        });

        // Fungsi untuk toggle menu mobile
        function toggleMobileMenu() {
            const mobileMenu = document.querySelector('.mobile-menu');
            if (mobileMenu) {
                mobileMenu.classList.toggle('hidden');
            }
        }
    </script>
    
    @stack('scripts')
</body>
</html>