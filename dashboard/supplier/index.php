<?php
// dashboard/supplier/index.php
// Supplier dashboard for KDMP application

require_once __DIR__ . '/../base_dashboard.php';

// Get supplier-specific stats
function get_supplier_stats($userId) {
    try {
        $crud = new DatabaseCRUD();
        
        // Get supplier statistics
        $total_products = $crud->count('produk', ['id_supplier' => $userId]);
        $total_orders = $crud->fetchOne(
            "SELECT COUNT(*) as count FROM pesanan p 
             JOIN detail_pesanan dp ON p.id = dp.id_pesanan 
             JOIN produk pr ON dp.id_produk = pr.id 
             WHERE pr.id_supplier = :supplier_id",
            ['supplier_id' => $userId]
        );
        
        $completed_orders = $crud->fetchOne(
            "SELECT COUNT(*) as count FROM pesanan p 
             JOIN detail_pesanan dp ON p.id = dp.id_pesanan 
             JOIN produk pr ON dp.id_produk = pr.id 
             WHERE pr.id_supplier = :supplier_id AND p.status_pesanan = 'delivered'",
            ['supplier_id' => $userId]
        );
        
        $revenue = $crud->fetchOne(
            "SELECT SUM(p.total_harga) as revenue FROM pesanan p 
             JOIN detail_pesanan dp ON p.id = dp.id_pesanan 
             JOIN produk pr ON dp.id_produk = pr.id 
             WHERE pr.id_supplier = :supplier_id AND p.status_pesanan = 'delivered'",
            ['supplier_id' => $userId]
        );
        
        return [
            'total_products' => $total_products['success'] ? $total_products['count'] : 0,
            'total_orders' => $total_orders ? $total_orders['count'] : 0,
            'completed_orders' => $completed_orders ? $completed_orders['count'] : 0,
            'revenue' => $revenue && $revenue['revenue'] ? $revenue['revenue'] : 0
        ];
    } catch (Exception $e) {
        return [
            'total_products' => 0,
            'total_orders' => 0,
            'completed_orders' => 0,
            'revenue' => 0
        ];
    }
}

$stats = get_supplier_stats($user['id']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Supplier - Dapur Suplai</title>
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
        .gradient-bg {
            background: linear-gradient(135deg, #DC2626 0%, #991B1B 50%, #7F1D1D 100%);
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
                    <div class="w-12 h-12 rounded-full flex items-center justify-center">
                         <img src="https://www.appdapursuplai.org/images/logo.png" alt="Logo" width="300" height="300">
                    </div>
                    <div>
                        <h1 class="text-xl font-normal text-merah tracking-tight">DAPUR SUPLAI</h1>
                        <p class="text-xs text-gray-600 font-medium">Supplier Dashboard</p>
                    </div>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="index.php" class="text-merah font-semibold transition">Dashboard</a>
                    <a href="products.php" class="text-gray-700 hover:text-merah font-semibold transition">Produk Saya</a>
                    <a href="orders.php" class="text-gray-700 hover:text-merah font-semibold transition">Pesanan Saya</a>
                    <a href="profile.php" class="text-gray-700 hover:text-merah font-semibold transition">Profil</a>
                    <a href="../../auth/logout.php" class="text-gray-700 hover:text-merah font-semibold transition">Logout</a>
                </div>

                <!-- Mobile Menu -->
                <div class="mobile-menu hidden absolute top-20 left-0 right-0 bg-white shadow-xl z-50 md:hidden">
                    <div class="flex flex-col py-4 space-y-4 px-6">
                        <a href="index.php" class="text-merah font-semibold transition py-2">Dashboard</a>
                        <a href="products.php" class="text-gray-700 hover:text-merah font-semibold transition py-2">Produk Saya</a>
                        <a href="orders.php" class="text-gray-700 hover:text-merah font-semibold transition py-2">Pesanan Saya</a>
                        <a href="profile.php" class="text-gray-700 hover:text-merah font-semibold transition py-2">Profil</a>
                        <a href="../../auth/logout.php" class="text-gray-700 hover:text-merah font-semibold transition py-2">Logout</a>
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
                <h1 class="text-4xl font-bold text-gray-800 mb-4">Selamat Datang, <?php echo htmlspecialchars($user['nama_lengkap'] ?? $user['username']); ?>!</h1>
                <p class="text-gray-600">Dashboard supplier untuk mengelola produk dan pesanan Anda</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                <div class="bg-white rounded-2xl shadow-lg p-6 card-hover">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-box text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500">Total Produk</p>
                            <p class="text-2xl font-bold text-gray-800"><?php echo $stats['total_products']; ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-6 card-hover">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <i class="fas fa-shopping-cart text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500">Total Pesanan</p>
                            <p class="text-2xl font-bold text-gray-800"><?php echo $stats['total_orders']; ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-6 card-hover">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                            <i class="fas fa-check-circle text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500">Pesanan Selesai</p>
                            <p class="text-2xl font-bold text-gray-800"><?php echo $stats['completed_orders']; ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-6 card-hover">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <i class="fas fa-coins text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500">Pendapatan</p>
                            <p class="text-2xl font-bold text-gray-800"><?php echo format_currency($stats['revenue']); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-12">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Pesanan Terbaru untuk Produk Saya</h2>
                    <a href="orders.php" class="text-merah font-medium hover:underline">Lihat Semua</a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="py-3 px-4 text-left text-gray-600 font-semibold">ID Pesanan</th>
                                <th class="py-3 px-4 text-left text-gray-600 font-semibold">Customer</th>
                                <th class="py-3 px-4 text-left text-gray-600 font-semibold">Tanggal</th>
                                <th class="py-3 px-4 text-left text-gray-600 font-semibold">Jumlah Produk Saya</th>
                                <th class="py-3 px-4 text-left text-gray-600 font-semibold">Status</th>
                                <th class="py-3 px-4 text-left text-gray-600 font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                $crud = new DatabaseCRUD();
                                
                                // Get orders that contain products from this supplier
                                $recent_orders = $crud->fetchAll(
                                    "SELECT DISTINCT p.id, p.id_user, p.tanggal_pesan, p.status_pesanan, 
                                            COUNT(dp.id) as product_count
                                     FROM pesanan p
                                     JOIN detail_pesanan dp ON p.id = dp.id_pesanan
                                     JOIN produk pr ON dp.id_produk = pr.id
                                     WHERE pr.id_supplier = :supplier_id
                                     ORDER BY p.tanggal_pesan DESC
                                     LIMIT 5",
                                    ['supplier_id' => $user['id']]
                                );
                                
                                if (!empty($recent_orders)) {
                                    foreach ($recent_orders as $order) {
                                        // Get customer name
                                        $customer = $crud->findById('users', $order['id_user']);
                                        $customer_name = $customer['success'] ? 
                                            ($customer['data']['nama_lengkap'] ?? $customer['data']['username']) : 
                                            'Unknown';
                                        
                                        $status_class = '';
                                        $status_text = ucfirst(str_replace('_', ' ', $order['status_pesanan']));
                                        
                                        switch ($order['status_pesanan']) {
                                            case 'delivered':
                                                $status_class = 'bg-green-100 text-green-800';
                                                break;
                                            case 'shipped':
                                                $status_class = 'bg-blue-100 text-blue-800';
                                                break;
                                            case 'processing':
                                            case 'confirmed':
                                                $status_class = 'bg-yellow-100 text-yellow-800';
                                                break;
                                            case 'cancelled':
                                                $status_class = 'bg-red-100 text-red-800';
                                                break;
                                            default:
                                                $status_class = 'bg-gray-100 text-gray-800';
                                        }
                                        ?>
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="py-3 px-4">#<?php echo htmlspecialchars($order['id']); ?></td>
                                            <td class="py-3 px-4"><?php echo htmlspecialchars($customer_name); ?></td>
                                            <td class="py-3 px-4"><?php echo date('d M Y', strtotime($order['tanggal_pesan'])); ?></td>
                                            <td class="py-3 px-4"><?php echo $order['product_count']; ?></td>
                                            <td class="py-3 px-4">
                                                <span class="px-2 py-1 rounded-full text-xs <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <a href="order_detail.php?id=<?php echo $order['id']; ?>" class="text-merah hover:underline">Detail</a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="6" class="py-8 px-4 text-center text-gray-500">
                                            <p>Belum ada pesanan untuk produk Anda.</p>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } catch (Exception $e) {
                                ?>
                                <tr>
                                    <td colspan="6" class="py-8 px-4 text-center text-red-500">
                                        <p>Terjadi kesalahan saat memuat pesanan.</p>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="products.php" class="bg-white rounded-2xl shadow-lg p-6 card-hover flex items-center">
                    <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                        <i class="fas fa-box-open text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-bold text-lg text-gray-800">Kelola Produk</h3>
                        <p class="text-gray-600">Tambah atau edit produk Anda</p>
                    </div>
                </a>

                <a href="orders.php" class="bg-white rounded-2xl shadow-lg p-6 card-hover flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-receipt text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-bold text-lg text-gray-800">Lihat Pesanan</h3>
                        <p class="text-gray-600">Lihat dan proses pesanan produk Anda</p>
                    </div>
                </a>

                <a href="profile.php" class="bg-white rounded-2xl shadow-lg p-6 card-hover flex items-center">
                    <div class="p-3 rounded-full bg-pink-100 text-pink-600">
                        <i class="fas fa-user text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-bold text-lg text-gray-800">Edit Profil</h3>
                        <p class="text-gray-600">Ubah informasi akun Anda</p>
                    </div>
                </a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gelap text-white py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p>&copy; 2024 Dapur Suplai. Hak Cipta Dilindungi.</p>
        </div>
    </footer>

    <script>
        // Fungsi untuk toggle menu mobile
        function toggleMobileMenu() {
            const mobileMenu = document.querySelector('.mobile-menu');
            if (mobileMenu) {
                mobileMenu.classList.toggle('hidden');
            }
        }
    </script>
</body>
</html>