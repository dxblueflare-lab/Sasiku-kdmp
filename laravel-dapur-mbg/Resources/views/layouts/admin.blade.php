<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Dapur Suplai</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        .sidebar {
            width: 250px;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: fixed;
            height: 100%;
            overflow-y: auto;
            z-index: 10;
        }
        .main-content {
            margin-left: 250px;
        }
        .sidebar.hidden {
            transform: translateX(-100%);
        }
        .mobile-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100%;
            background: white;
            z-index: 50;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }
        .mobile-sidebar.active {
            transform: translateX(0);
        }
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 40;
        }
        .overlay.active {
            display: block;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <!-- Mobile Overlay -->
    <div class="overlay" id="overlay"></div>

    <!-- Mobile Sidebar -->
    <div class="mobile-sidebar glass-effect" id="mobileSidebar">
        <div class="p-6">
            <div class="flex items-center space-x-3 mb-8">
                <div class="w-12 h-12 bg-gradient-to-br from-merah to-red-800 rounded-full flex items-center justify-center shadow-lg">
                     <img src="https://www.appdapursuplai.org/images/logo.png" alt="Logo" width="50" height="50">
                </div>
                <div>
                    <h1 class="text-xl font-bold text-merah tracking-tight">DAPUR SUPLAI</h1>
                    <p class="text-xs text-gray-600 font-medium">Admin Panel</p>
                </div>
            </div>

            <nav class="space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-merah text-white rounded-lg' : 'text-gray-700 hover:bg-red-50 hover:text-merah rounded-lg' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.products.index') }}" class="flex items-center space-x-3 px-4 py-3 {{ request()->routeIs('admin.products.*') ? 'bg-merah text-white rounded-lg' : 'text-gray-700 hover:bg-red-50 hover:text-merah rounded-lg' }}">
                    <i class="fas fa-box"></i>
                    <span>Produk</span>
                </a>
                <a href="{{ route('admin.orders.index') }}" class="flex items-center space-x-3 px-4 py-3 {{ request()->routeIs('admin.orders.*') ? 'bg-merah text-white rounded-lg' : 'text-gray-700 hover:bg-red-50 hover:text-merah rounded-lg' }}">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Pesanan</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center space-x-3 px-4 py-3 {{ request()->routeIs('admin.users.*') ? 'bg-merah text-white rounded-lg' : 'text-gray-700 hover:bg-red-50 hover:text-merah rounded-lg' }}">
                    <i class="fas fa-users"></i>
                    <span>Pengguna</span>
                </a>
                <a href="{{ route('admin.menu-updates.index') }}" class="flex items-center space-x-3 px-4 py-3 {{ request()->routeIs('admin.menu-updates.*') ? 'bg-merah text-white rounded-lg' : 'text-gray-700 hover:bg-red-50 hover:text-merah rounded-lg' }}">
                    <i class="fas fa-utensils"></i>
                    <span>Update Menu</span>
                </a>
                <a href="{{ route('admin.reports.index') }}" class="flex items-center space-x-3 px-4 py-3 {{ request()->routeIs('admin.reports.*') ? 'bg-merah text-white rounded-lg' : 'text-gray-700 hover:bg-red-50 hover:text-merah rounded-lg' }}">
                    <i class="fas fa-chart-bar"></i>
                    <span>Laporan</span>
                </a>
                <a href="{{ route('logout') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-red-50 hover:text-merah rounded-lg">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </nav>
        </div>
    </div>

    <!-- Desktop Sidebar -->
    <div class="sidebar glass-effect" id="desktopSidebar">
        <div class="p-6">
            <div class="flex items-center space-x-3 mb-8">
                <div class="w-12 h-12 bg-gradient-to-br from-merah to-red-800 rounded-full flex items-center justify-center shadow-lg">
                     <img src="https://www.appdapursuplai.org/images/logo.png" alt="Logo" width="50" height="50">
                </div>
                <div>
                    <h1 class="text-xl font-bold text-merah tracking-tight">DAPUR SUPLAI</h1>
                    <p class="text-xs text-gray-600 font-medium">Admin Panel</p>
                </div>
            </div>

            <nav class="space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-merah text-white rounded-lg' : 'text-gray-700 hover:bg-red-50 hover:text-merah rounded-lg' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.products.index') }}" class="flex items-center space-x-3 px-4 py-3 {{ request()->routeIs('admin.products.*') ? 'bg-merah text-white rounded-lg' : 'text-gray-700 hover:bg-red-50 hover:text-merah rounded-lg' }}">
                    <i class="fas fa-box"></i>
                    <span>Produk</span>
                </a>
                <a href="{{ route('admin.orders.index') }}" class="flex items-center space-x-3 px-4 py-3 {{ request()->routeIs('admin.orders.*') ? 'bg-merah text-white rounded-lg' : 'text-gray-700 hover:bg-red-50 hover:text-merah rounded-lg' }}">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Pesanan</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center space-x-3 px-4 py-3 {{ request()->routeIs('admin.users.*') ? 'bg-merah text-white rounded-lg' : 'text-gray-700 hover:bg-red-50 hover:text-merah rounded-lg' }}">
                    <i class="fas fa-users"></i>
                    <span>Pengguna</span>
                </a>
                <a href="{{ route('admin.menu-updates.index') }}" class="flex items-center space-x-3 px-4 py-3 {{ request()->routeIs('admin.menu-updates.*') ? 'bg-merah text-white rounded-lg' : 'text-gray-700 hover:bg-red-50 hover:text-merah rounded-lg' }}">
                    <i class="fas fa-utensils"></i>
                    <span>Update Menu</span>
                </a>
                <a href="{{ route('admin.reports.index') }}" class="flex items-center space-x-3 px-4 py-3 {{ request()->routeIs('admin.reports.*') ? 'bg-merah text-white rounded-lg' : 'text-gray-700 hover:bg-red-50 hover:text-merah rounded-lg' }}">
                    <i class="fas fa-chart-bar"></i>
                    <span>Laporan</span>
                </a>
                <a href="{{ route('logout') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-red-50 hover:text-merah rounded-lg">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <header class="glass-effect shadow-lg p-4 flex justify-between items-center">
            <div class="flex items-center">
                <button id="menuToggle" class="md:hidden mr-4 text-merah text-2xl">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">@yield('title')</h1>
                    <p class="text-gray-600">@yield('subtitle', 'Kelola sistem e-commerce Dapur Suplai')</p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <i class="fas fa-bell text-gray-600 text-xl"></i>
                    <span class="absolute -top-1 -right-1 bg-emas text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">3</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-merah rounded-full flex items-center justify-center text-white font-bold">
                        {{ substr(Auth::user()->nama_lengkap ?? Auth::user()->username, 0, 2) }}
                    </div>
                    <span class="font-semibold">{{ Auth::user()->nama_lengkap ?? Auth::user()->username }}</span>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="p-6">
            @yield('content')
        </main>
    </div>

    <script>
        // Toggle mobile sidebar
        document.getElementById('menuToggle').addEventListener('click', function() {
            const mobileSidebar = document.getElementById('mobileSidebar');
            const overlay = document.getElementById('overlay');

            mobileSidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        });

        // Close sidebar when clicking on overlay
        document.getElementById('overlay').addEventListener('click', function() {
            document.getElementById('mobileSidebar').classList.remove('active');
            this.classList.remove('active');
        });

        // Additional scripts section
        @yield('scripts')
    </script>
</body>
</html>