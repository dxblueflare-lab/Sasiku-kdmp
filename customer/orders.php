<?php
// customer/orders.php
// Order history page for customers

require_once __DIR__ . '/../includes/DatabaseConfig.php';
require_once __DIR__ . '/../includes/DatabaseStorage.php';
require_once __DIR__ . '/../includes/DatabaseCRUD.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit();
}

$user = $_SESSION['user'];

// Get user's orders
try {
    $crud = new DatabaseCRUD();
    $orders = $crud->read('pesanan', ['id_user' => $user['id']], '*', 'tanggal_pesan DESC');
} catch (Exception $e) {
    $orders = ['success' => false, 'data' => []];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan - Dapur Suplai</title>
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
                    <div class="w-12 h-12 rounded-full flex items-center justify-center">
                         <img src="https://www.appdapursuplai.org/images/logo.png" alt="Logo" width="300" height="300">
                    </div>
                    <div>
                        <h1 class="text-xl font-normal text-merah tracking-tight">DAPUR SUPLAI</h1>
                        <p class="text-xs text-gray-600 font-medium">Riwayat Pesanan</p>
                    </div>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="../index.php" class="text-gray-700 hover:text-merah font-semibold transition">Beranda</a>
                    <a href="../shop.php" class="text-gray-700 hover:text-merah font-semibold transition">Belanja</a>
                    <a href="orders.php" class="text-merah font-semibold transition">Pesanan Saya</a>
                    <a href="profile.php" class="text-gray-700 hover:text-merah font-semibold transition">Profil</a>
                    <a href="../auth/logout.php" class="text-gray-700 hover:text-merah font-semibold transition">Logout</a>
                </div>

                <!-- Mobile Menu -->
                <div class="mobile-menu hidden absolute top-20 left-0 right-0 bg-white shadow-xl z-50 md:hidden">
                    <div class="flex flex-col py-4 space-y-4 px-6">
                        <a href="../index.php" class="text-gray-700 hover:text-merah font-semibold transition py-2">Beranda</a>
                        <a href="../shop.php" class="text-gray-700 hover:text-merah font-semibold transition py-2">Belanja</a>
                        <a href="orders.php" class="text-merah font-semibold transition py-2">Pesanan Saya</a>
                        <a href="profile.php" class="text-gray-700 hover:text-merah font-semibold transition py-2">Profil</a>
                        <a href="../auth/logout.php" class="text-gray-700 hover:text-merah font-semibold transition py-2">Logout</a>
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
                <h1 class="text-4xl font-bold text-gray-800 mb-4">Riwayat Pesanan</h1>
                <p class="text-gray-600">Lihat dan lacak status pesanan Anda</p>
            </div>

            <!-- Orders Table -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="py-3 px-4 text-left text-gray-600 font-semibold">ID Pesanan</th>
                                <th class="py-3 px-4 text-left text-gray-600 font-semibold">Tanggal</th>
                                <th class="py-3 px-4 text-left text-gray-600 font-semibold">Total</th>
                                <th class="py-3 px-4 text-left text-gray-600 font-semibold">Status</th>
                                <th class="py-3 px-4 text-left text-gray-600 font-semibold">Metode Pembayaran</th>
                                <th class="py-3 px-4 text-left text-gray-600 font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($orders['success'] && !empty($orders['data'])): ?>
                            <?php foreach ($orders['data'] as $order): ?>
                            <?php
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
                                <td class="py-3 px-4"><?php echo date('d M Y', strtotime($order['tanggal_pesan'])); ?></td>
                                <td class="py-3 px-4"><?php echo format_currency($order['total_harga']); ?></td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-1 rounded-full text-xs <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                </td>
                                <td class="py-3 px-4"><?php echo htmlspecialchars($order['metode_pembayaran'] ?? 'N/A'); ?></td>
                                <td class="py-3 px-4">
                                    <a href="order_detail.php?id=<?php echo $order['id']; ?>" class="text-merah hover:underline">Detail</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="6" class="py-8 px-4 text-center text-gray-500">
                                    <p>Anda belum memiliki pesanan.</p>
                                    <a href="../shop.php" class="mt-4 inline-block bg-merah text-white px-6 py-2 rounded-full font-semibold hover:bg-red-700 transition">Mulai Belanja</a>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
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
        
        // Helper function to format currency
        function formatCurrency(amount) {
            return 'Rp ' + amount.toLocaleString('id-ID');
        }
    </script>
</body>
</html>

<?php
function format_currency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}
?>