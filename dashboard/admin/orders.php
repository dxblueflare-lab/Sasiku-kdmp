<?php
// dashboard/admin/orders.php
// Order management page for admin

require_once __DIR__ . '/../base_dashboard.php';

// Check if user has admin role
if ($user['role'] !== 'admin') {
    header('Location: ../../index.php');
    exit();
}

// Handle form submissions
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $crud = new DatabaseCRUD();
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_status':
                $id = (int)($_POST['id'] ?? 0);
                $status = $_POST['status'] ?? '';
                
                if ($id > 0 && !empty($status)) {
                    $result = $crud->update('pesanan', ['status_pesanan' => $status], ['id' => $id]);
                    if ($result) {
                        $message = 'Status pesanan berhasil diperbarui';
                        $message_type = 'success';
                    } else {
                        $message = 'Gagal memperbarui status pesanan';
                        $message_type = 'error';
                    }
                } else {
                    $message = 'Data tidak lengkap';
                    $message_type = 'error';
                }
                break;
        }
    }
}

// Get all orders
try {
    $crud = new DatabaseCRUD();
    $orders = $crud->read('pesanan', [], '*', 'tanggal_pesan DESC');
} catch (Exception $e) {
    $orders = ['success' => false, 'data' => []];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pesanan - Admin Dashboard</title>
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
                        <p class="text-xs text-gray-600 font-medium">Admin Dashboard</p>
                    </div>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="index.php" class="text-gray-700 hover:text-merah font-semibold transition">Dashboard</a>
                    <a href="users.php" class="text-gray-700 hover:text-merah font-semibold transition">Pengguna</a>
                    <a href="products.php" class="text-gray-700 hover:text-merah font-semibold transition">Produk</a>
                    <a href="orders.php" class="text-merah font-semibold transition">Pesanan</a>
                    <a href="reports.php" class="text-gray-700 hover:text-merah font-semibold transition">Laporan</a>
                    <a href="../../auth/logout.php" class="text-gray-700 hover:text-merah font-semibold transition">Logout</a>
                </div>

                <!-- Mobile Menu -->
                <div class="mobile-menu hidden absolute top-20 left-0 right-0 bg-white shadow-xl z-50 md:hidden">
                    <div class="flex flex-col py-4 space-y-4 px-6">
                        <a href="index.php" class="text-gray-700 hover:text-merah font-semibold transition py-2">Dashboard</a>
                        <a href="users.php" class="text-gray-700 hover:text-merah font-semibold transition py-2">Pengguna</a>
                        <a href="products.php" class="text-gray-700 hover:text-merah font-semibold transition py-2">Produk</a>
                        <a href="orders.php" class="text-merah font-semibold transition py-2">Pesanan</a>
                        <a href="reports.php" class="text-gray-700 hover:text-merah font-semibold transition py-2">Laporan</a>
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
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Manajemen Pesanan</h1>
            </div>

            <?php if ($message): ?>
            <div class="mb-6 p-4 rounded-lg <?php echo $message_type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                <i class="fas <?php echo $message_type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?> mr-2"></i>
                <?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>

            <!-- Orders Table -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="py-3 px-4 text-left text-gray-600 font-semibold">ID Pesanan</th>
                                <th class="py-3 px-4 text-left text-gray-600 font-semibold">Customer</th>
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
                                <td class="py-3 px-4"><?php echo format_currency($order['total_harga']); ?></td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-1 rounded-full text-xs <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                </td>
                                <td class="py-3 px-4"><?php echo htmlspecialchars($order['metode_pembayaran'] ?? 'N/A'); ?></td>
                                <td class="py-3 px-4">
                                    <div class="flex space-x-2">
                                        <button onclick="openStatusModal(<?php echo $order['id']; ?>, '<?php echo $order['status_pesanan']; ?>')" class="text-blue-500 hover:text-blue-700">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="order_detail.php?id=<?php echo $order['id']; ?>" class="text-merah hover:text-red-700">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="7" class="py-8 px-4 text-center text-gray-500">
                                    <p>Belum ada pesanan.</p>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Update Status Modal -->
    <div id="status-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeStatusModal()"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white shadow-2xl rounded-2xl p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Update Status Pesanan</h3>
                <button onclick="closeStatusModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <form id="status-form" method="POST">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" id="order-id" name="id" value="">

                <div class="mb-6">
                    <label for="status" class="block text-gray-700 font-medium mb-2">Status Pesanan</label>
                    <select 
                        id="status" 
                        name="status" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-merah focus:border-transparent outline-none transition"
                        required
                    >
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="processing">Processing</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeStatusModal()" class="px-6 py-3 border border-gray-300 rounded-full font-semibold hover:bg-gray-100 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-3 bg-merah text-white rounded-full font-semibold hover:bg-red-700 transition">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gelap text-white py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p>&copy; 2024 Dapur Suplai. Hak Cipta Dilindungi.</p>
        </div>
    </footer>

    <script>
        // Open status modal
        function openStatusModal(orderId, currentStatus) {
            document.getElementById('order-id').value = orderId;
            document.getElementById('status').value = currentStatus;
            document.getElementById('status-modal').classList.remove('hidden');
        }

        // Close status modal
        function closeStatusModal() {
            document.getElementById('status-modal').classList.add('hidden');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const statusModal = document.getElementById('status-modal');
            
            if (event.target === statusModal.parentElement) {
                closeStatusModal();
            }
        }

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