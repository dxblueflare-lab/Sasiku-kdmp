<?php
// dashboard/supplier/products.php
// Product management page for supplier

require_once __DIR__ . '/../base_dashboard.php';

// Check if user has supplier role
if ($user['role'] !== 'supplier') {
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
            case 'add':
                $nama_produk = trim($_POST['nama_produk'] ?? '');
                $deskripsi = trim($_POST['deskripsi'] ?? '');
                $harga = (float)($_POST['harga'] ?? 0);
                $stok = (int)($_POST['stok'] ?? 0);
                $id_kategori = (int)($_POST['id_kategori'] ?? 0);
                
                if (!empty($nama_produk) && $harga > 0 && $id_kategori > 0) {
                    $product_data = [
                        'nama_produk' => $nama_produk,
                        'deskripsi' => $deskripsi,
                        'harga' => $harga,
                        'stok' => $stok,
                        'id_kategori' => $id_kategori,
                        'id_supplier' => $user['id'] // Set supplier to current user
                    ];
                    
                    $result = $crud->create('produk', $product_data);
                    if ($result['success']) {
                        $message = 'Produk berhasil ditambahkan';
                        $message_type = 'success';
                    } else {
                        $message = 'Gagal menambahkan produk: ' . $result['message'];
                        $message_type = 'error';
                    }
                } else {
                    $message = 'Mohon lengkapi semua field yang wajib';
                    $message_type = 'error';
                }
                break;
                
            case 'update':
                $id = (int)($_POST['id'] ?? 0);
                $nama_produk = trim($_POST['nama_produk'] ?? '');
                $deskripsi = trim($_POST['deskripsi'] ?? '');
                $harga = (float)($_POST['harga'] ?? 0);
                $stok = (int)($_POST['stok'] ?? 0);
                $id_kategori = (int)($_POST['id_kategori'] ?? 0);
                
                // Verify that the product belongs to the current supplier
                $product = $crud->findById('produk', $id);
                if ($product['success'] && $product['data']['id_supplier'] == $user['id']) {
                    if ($id > 0 && !empty($nama_produk) && $harga > 0 && $id_kategori > 0) {
                        $product_data = [
                            'nama_produk' => $nama_produk,
                            'deskripsi' => $deskripsi,
                            'harga' => $harga,
                            'stok' => $stok,
                            'id_kategori' => $id_kategori
                        ];
                        
                        $result = $crud->update('produk', $product_data, ['id' => $id]);
                        if ($result) {
                            $message = 'Produk berhasil diperbarui';
                            $message_type = 'success';
                        } else {
                            $message = 'Gagal memperbarui produk';
                            $message_type = 'error';
                        }
                    } else {
                        $message = 'Mohon lengkapi semua field yang wajib';
                        $message_type = 'error';
                    }
                } else {
                    $message = 'Anda tidak memiliki izin untuk mengedit produk ini';
                    $message_type = 'error';
                }
                break;
                
            case 'delete':
                $id = (int)($_POST['id'] ?? 0);
                
                // Verify that the product belongs to the current supplier
                $product = $crud->findById('produk', $id);
                if ($product['success'] && $product['data']['id_supplier'] == $user['id']) {
                    if ($id > 0) {
                        $result = $crud->delete('produk', ['id' => $id]);
                        if ($result) {
                            $message = 'Produk berhasil dihapus';
                            $message_type = 'success';
                        } else {
                            $message = 'Gagal menghapus produk';
                            $message_type = 'error';
                        }
                    }
                } else {
                    $message = 'Anda tidak memiliki izin untuk menghapus produk ini';
                    $message_type = 'error';
                }
                break;
        }
    }
}

// Get products for this supplier
try {
    $crud = new DatabaseCRUD();
    $products = $crud->read('produk', ['id_supplier' => $user['id']], '*', 'id DESC');
    
    // Get categories
    $categories = $crud->read('kategori_produk', [], '*', 'id ASC');
} catch (Exception $e) {
    $products = ['success' => false, 'data' => []];
    $categories = ['success' => false, 'data' => []];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk Saya - Supplier Dashboard</title>
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
                        <p class="text-xs text-gray-600 font-medium">Supplier Dashboard</p>
                    </div>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="index.php" class="text-gray-700 hover:text-merah font-semibold transition">Dashboard</a>
                    <a href="products.php" class="text-merah font-semibold transition">Produk Saya</a>
                    <a href="orders.php" class="text-gray-700 hover:text-merah font-semibold transition">Pesanan Saya</a>
                    <a href="profile.php" class="text-gray-700 hover:text-merah font-semibold transition">Profil</a>
                    <a href="../../auth/logout.php" class="text-gray-700 hover:text-merah font-semibold transition">Logout</a>
                </div>

                <!-- Mobile Menu -->
                <div class="mobile-menu hidden absolute top-20 left-0 right-0 bg-white shadow-xl z-50 md:hidden">
                    <div class="flex flex-col py-4 space-y-4 px-6">
                        <a href="index.php" class="text-gray-700 hover:text-merah font-semibold transition py-2">Dashboard</a>
                        <a href="products.php" class="text-merah font-semibold transition py-2">Produk Saya</a>
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
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Produk Saya</h1>
                <button onclick="openAddModal()" class="bg-merah text-white px-6 py-3 rounded-full font-semibold hover:bg-red-700 transition flex items-center space-x-2">
                    <i class="fas fa-plus"></i>
                    <span>Tambah Produk</span>
                </button>
            </div>

            <?php if ($message): ?>
            <div class="mb-6 p-4 rounded-lg <?php echo $message_type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                <i class="fas <?php echo $message_type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?> mr-2"></i>
                <?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>

            <!-- Products Table -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="py-3 px-4 text-left text-gray-600 font-semibold">ID</th>
                                <th class="py-3 px-4 text-left text-gray-600 font-semibold">Nama Produk</th>
                                <th class="py-3 px-4 text-left text-gray-600 font-semibold">Kategori</th>
                                <th class="py-3 px-4 text-left text-gray-600 font-semibold">Harga</th>
                                <th class="py-3 px-4 text-left text-gray-600 font-semibold">Stok</th>
                                <th class="py-3 px-4 text-left text-gray-600 font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($products['success'] && !empty($products['data'])): ?>
                            <?php foreach ($products['data'] as $product): ?>
                            <?php
                                // Get category name
                                $category = $crud->findById('kategori_produk', $product['id_kategori']);
                                $category_name = $category['success'] ? $category['data']['nama_kategori'] : 'Tidak Diketahui';
                            ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 px-4"><?php echo htmlspecialchars($product['id']); ?></td>
                                <td class="py-3 px-4 font-medium"><?php echo htmlspecialchars($product['nama_produk']); ?></td>
                                <td class="py-3 px-4"><?php echo htmlspecialchars($category_name); ?></td>
                                <td class="py-3 px-4"><?php echo format_currency($product['harga']); ?></td>
                                <td class="py-3 px-4"><?php echo htmlspecialchars($product['stok']); ?></td>
                                <td class="py-3 px-4">
                                    <div class="flex space-x-2">
                                        <button onclick="openEditModal(<?php echo $product['id']; ?>, '<?php echo addslashes(htmlspecialchars($product['nama_produk'])); ?>', '<?php echo addslashes(htmlspecialchars($product['deskripsi'])); ?>', <?php echo $product['harga']; ?>, <?php echo $product['stok']; ?>, <?php echo $product['id_kategori']; ?>)" class="text-blue-500 hover:text-blue-700">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="confirmDelete(<?php echo $product['id']; ?>)" class="text-red-500 hover:text-red-700">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="6" class="py-8 px-4 text-center text-gray-500">
                                    <p>Belum ada produk.</p>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Add/Edit Product Modal -->
    <div id="product-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal()"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl bg-white shadow-2xl rounded-2xl p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 id="modal-title" class="text-2xl font-bold text-gray-800">Tambah Produk</h3>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <form id="product-form" method="POST">
                <input type="hidden" id="form-action" name="action" value="add">
                <input type="hidden" id="product-id" name="id" value="">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nama_produk" class="block text-gray-700 font-medium mb-2">Nama Produk *</label>
                        <input 
                            type="text" 
                            id="nama_produk" 
                            name="nama_produk" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-merah focus:border-transparent outline-none transition"
                            placeholder="Masukkan nama produk"
                            required
                        >
                    </div>

                    <div>
                        <label for="id_kategori" class="block text-gray-700 font-medium mb-2">Kategori *</label>
                        <select 
                            id="id_kategori" 
                            name="id_kategori" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-merah focus:border-transparent outline-none transition"
                            required
                        >
                            <option value="">Pilih Kategori</option>
                            <?php if ($categories['success'] && !empty($categories['data'])): ?>
                            <?php foreach ($categories['data'] as $category): ?>
                            <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['nama_kategori']); ?></option>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div>
                        <label for="harga" class="block text-gray-700 font-medium mb-2">Harga *</label>
                        <input 
                            type="number" 
                            id="harga" 
                            name="harga" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-merah focus:border-transparent outline-none transition"
                            placeholder="Masukkan harga"
                            min="0"
                            step="0.01"
                            required
                        >
                    </div>

                    <div>
                        <label for="stok" class="block text-gray-700 font-medium mb-2">Stok</label>
                        <input 
                            type="number" 
                            id="stok" 
                            name="stok" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-merah focus:border-transparent outline-none transition"
                            placeholder="Masukkan jumlah stok"
                            min="0"
                            value="0"
                        >
                    </div>

                    <div class="md:col-span-2">
                        <label for="deskripsi" class="block text-gray-700 font-medium mb-2">Deskripsi</label>
                        <textarea 
                            id="deskripsi" 
                            name="deskripsi" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-merah focus:border-transparent outline-none transition"
                            placeholder="Masukkan deskripsi produk"
                            rows="4"
                        ></textarea>
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-4">
                    <button type="button" onclick="closeModal()" class="px-6 py-3 border border-gray-300 rounded-full font-semibold hover:bg-gray-100 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-3 bg-merah text-white rounded-full font-semibold hover:bg-red-700 transition">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white shadow-2xl rounded-2xl p-6">
            <div class="text-center">
                <i class="fas fa-exclamation-triangle text-5xl text-red-500 mb-4"></i>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Konfirmasi Hapus</h3>
                <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menghapus produk ini? Tindakan ini tidak dapat dibatalkan.</p>
                <form id="delete-form" method="POST">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" id="delete-product-id" name="id" value="">
                    <div class="flex justify-center space-x-4">
                        <button type="button" onclick="closeDeleteModal()" class="px-6 py-3 border border-gray-300 rounded-full font-semibold hover:bg-gray-100 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-3 bg-red-500 text-white rounded-full font-semibold hover:bg-red-600 transition">
                            Hapus
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gelap text-white py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p>&copy; 2024 Dapur Suplai. Hak Cipta Dilindungi.</p>
        </div>
    </footer>

    <script>
        // Open add modal
        function openAddModal() {
            document.getElementById('modal-title').textContent = 'Tambah Produk';
            document.getElementById('form-action').value = 'add';
            document.getElementById('product-form').reset();
            document.getElementById('product-id').value = '';
            document.getElementById('product-modal').classList.remove('hidden');
        }

        // Open edit modal
        function openEditModal(id, nama_produk, deskripsi, harga, stok, id_kategori) {
            document.getElementById('modal-title').textContent = 'Edit Produk';
            document.getElementById('form-action').value = 'update';
            document.getElementById('product-id').value = id;
            document.getElementById('nama_produk').value = nama_produk;
            document.getElementById('deskripsi').value = deskripsi;
            document.getElementById('harga').value = harga;
            document.getElementById('stok').value = stok;
            document.getElementById('id_kategori').value = id_kategori;
            document.getElementById('product-modal').classList.remove('hidden');
        }

        // Close modal
        function closeModal() {
            document.getElementById('product-modal').classList.add('hidden');
        }

        // Confirm delete
        function confirmDelete(id) {
            document.getElementById('delete-product-id').value = id;
            document.getElementById('delete-modal').classList.remove('hidden');
        }

        // Close delete modal
        function closeDeleteModal() {
            document.getElementById('delete-modal').classList.add('hidden');
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const productModal = document.getElementById('product-modal');
            const deleteModal = document.getElementById('delete-modal');
            
            if (event.target === productModal.parentElement) {
                closeModal();
            }
            
            if (event.target === deleteModal.parentElement) {
                closeDeleteModal();
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