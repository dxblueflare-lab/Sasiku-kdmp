<?php
// customer/order_detail.php
// Order detail page for customers

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
$order_id = $_GET['id'] ?? 0;

if (!$order_id) {
    header('Location: orders.php');
    exit();
}

// Get order details
try {
    $crud = new DatabaseCRUD();
    $order = $crud->findById('pesanan', $order_id);
    
    // Verify that the order belongs to the current user
    if (!$order['success'] || $order['data']['id_user'] != $user['id']) {
        header('Location: orders.php');
        exit();
    }
    
    // Get order details
    $order_details = $crud->read('detail_pesanan', ['id_pesanan' => $order_id], '*', 'id ASC');
    
    // Get products for order details
    $products = [];
    if ($order_details['success'] && !empty($order_details['data'])) {
        foreach ($order_details['data'] as $detail) {
            $product = $crud->findById('produk', $detail['id_produk']);
            if ($product['success']) {
                $products[$detail['id_produk']] = $product['data'];
            }
        }
    }
} catch (Exception $e) {
    header('Location: orders.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan #<?php echo $order['data']['id']; ?> - Dapur Suplai</title>
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
                        <p class="text-xs text-gray-600 font-medium">Detail Pesanan</p>
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
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Detail Pesanan #<?php echo htmlspecialchars($order['data']['id']); ?></h1>
                <p class="text-gray-600">Tanggal: <?php echo date('d M Y H:i', strtotime($order['data']['tanggal_pesan'])); ?></p>
            </div>

            <!-- Order Status -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-800">Status Pesanan</h2>
                    <?php
                    $status_class = '';
                    $status_text = ucfirst(str_replace('_', ' ', $order['data']['status_pesanan']));
                    
                    switch ($order['data']['status_pesanan']) {
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
                    <span class="px-4 py-2 rounded-full text-lg font-semibold <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                </div>
                
                <div class="mt-6">
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <?php
                        $progress_width = 0;
                        switch ($order['data']['status_pesanan']) {
                            case 'delivered':
                                $progress_width = 100;
                                break;
                            case 'shipped':
                                $progress_width = 75;
                                break;
                            case 'processing':
                            case 'confirmed':
                                $progress_width = 50;
                                break;
                            case 'pending':
                                $progress_width = 25;
                                break;
                            default:
                                $progress_width = 0;
                        }
                        ?>
                        <div class="h-3 rounded-full <?php echo $order['data']['status_pesanan'] === 'delivered' ? 'bg-green-500' : 'bg-merah'; ?>" style="width: <?php echo $progress_width; ?>%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 mt-2">
                        <span>Pesanan Diterima</span>
                        <span>Diproses</span>
                        <span>Dikirim</span>
                        <span>Diterima</span>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Item Pesanan</h2>
                <div class="space-y-4">
                    <?php if ($order_details['success'] && !empty($order_details['data'])): ?>
                    <?php foreach ($order_details['data'] as $detail): ?>
                    <?php
                    $product = $products[$detail['id_produk']] ?? null;
                    ?>
                    <div class="flex items-center justify-between py-4 border-b">
                        <div class="flex items-center space-x-4">
                            <img src="<?php echo $product ? htmlspecialchars($product['gambar_produk'] ?? 'https://placehold.co/100x100?text=No+Image') : 'https://placehold.co/100x100?text=No+Image'; ?>" alt="<?php echo $product ? htmlspecialchars($product['nama_produk']) : 'Product'; ?>" class="w-16 h-16 object-cover rounded-lg">
                            <div>
                                <h4 class="font-semibold"><?php echo $product ? htmlspecialchars($product['nama_produk']) : 'Product Tidak Ditemukan'; ?></h4>
                                <p class="text-gray-600 text-sm">Rp <?php echo number_format($detail['harga_satuan'], 0, ',', '.'); ?> x <?php echo $detail['jumlah']; ?></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold">Rp <?php echo number_format($detail['subtotal'], 0, ',', '.'); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <p class="text-gray-500 text-center py-4">Tidak ada item dalam pesanan ini.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Ringkasan Pesanan</h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span>Subtotal:</span>
                        <span><?php echo format_currency($order['data']['total_harga']); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Biaya Pengiriman:</span>
                        <span><?php echo format_currency(0); // Assuming free shipping ?></span>
                    </div>
                    <div class="flex justify-between border-t pt-3 font-bold text-lg">
                        <span>Total:</span>
                        <span><?php echo format_currency($order['data']['total_harga']); ?></span>
                    </div>
                </div>

                <!-- Shipping Information -->
                <div class="mt-8">
                    <h3 class="text-lg font-bold text-gray-800 mb-3">Informasi Pengiriman</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="font-semibold"><?php echo htmlspecialchars($user['nama_lengkap'] ?? $user['username']); ?></p>
                        <p><?php echo htmlspecialchars($order['data']['alamat_pengiriman'] ?? 'Alamat tidak tersedia'); ?></p>
                        <p class="mt-2">Tanggal Pengiriman: <?php echo $order['data']['tanggal_pengiriman'] ? date('d M Y', strtotime($order['data']['tanggal_pengiriman'])) : 'Belum ditentukan'; ?></p>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="mt-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-3">Metode Pembayaran</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p><?php echo htmlspecialchars($order['data']['metode_pembayaran'] ?? 'Tidak tersedia'); ?></p>
                        <?php if ($order['data']['bukti_pembayaran']): ?>
                        <div class="mt-2">
                            <p class="text-sm text-gray-600">Bukti Pembayaran:</p>
                            <img src="<?php echo htmlspecialchars($order['data']['bukti_pembayaran']); ?>" alt="Bukti Pembayaran" class="mt-1 w-32 h-32 object-contain border">
                        </div>
                        <?php endif; ?>
                    </div>
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