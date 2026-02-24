<?php
// models/OrderModel.php
// Model untuk mengelola data pesanan dalam sistem Dapur Suplai

class OrderModel {
    private $conn;
    
    public function __construct($database) {
        $this->conn = $database->getConnection();
    }
    
    // Mendapatkan semua pesanan
    public function getAllOrders($limit = 10, $offset = 0) {
        $query = "SELECT o.*, u.nama_lengkap as nama_pelanggan, 
                  COUNT(od.id) as jumlah_item,
                  SUM(od.subtotal) as total_harga
                  FROM pesanan o
                  LEFT JOIN users u ON o.id_user = u.id
                  LEFT JOIN detail_pesanan od ON o.id = od.id_pesanan
                  GROUP BY o.id
                  ORDER BY o.tanggal_pesanan DESC
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Mendapatkan pesanan berdasarkan ID
    public function getOrderById($id) {
        $query = "SELECT o.*, u.nama_lengkap as nama_pelanggan, u.email, u.nomor_telepon, u.alamat,
                  COUNT(od.id) as jumlah_item,
                  SUM(od.subtotal) as total_harga
                  FROM pesanan o
                  LEFT JOIN users u ON o.id_user = u.id
                  LEFT JOIN detail_pesanan od ON o.id = od.id_pesanan
                  WHERE o.id = :id
                  GROUP BY o.id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Mendapatkan pesanan berdasarkan user
    public function getOrdersByUser($userId) {
        $query = "SELECT o.*, 
                  COUNT(od.id) as jumlah_item,
                  SUM(od.subtotal) as total_harga
                  FROM pesanan o
                  LEFT JOIN detail_pesanan od ON o.id = od.id_pesanan
                  WHERE o.id_user = :user_id
                  GROUP BY o.id
                  ORDER BY o.tanggal_pesanan DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Mendapatkan pesanan berdasarkan supplier
    public function getOrdersBySupplier($supplierId) {
        $query = "SELECT DISTINCT o.*, u.nama_lengkap as nama_pelanggan,
                  SUM(od.subtotal) as total_harga,
                  COUNT(od.id) as jumlah_produk
                  FROM pesanan o
                  JOIN detail_pesanan od ON o.id = od.id_pesanan
                  JOIN produk p ON od.id_produk = p.id
                  JOIN users u ON o.id_user = u.id
                  WHERE p.id_supplier = :supplier_id
                  GROUP BY o.id
                  ORDER BY o.tanggal_pesanan DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':supplier_id', $supplierId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Membuat pesanan baru
    public function createOrder($userId, $items, $shippingAddress, $paymentMethod) {
        try {
            $this->conn->beginTransaction();
            
            // Hitung total harga
            $totalPrice = 0;
            foreach($items as $item) {
                $totalPrice += $item['harga'] * $item['jumlah'];
            }
            
            // Buat pesanan utama
            $orderQuery = "INSERT INTO pesanan (id_user, total_harga, metode_pembayaran, alamat_pengiriman, status_pesanan, tanggal_pesanan) 
                          VALUES (:user_id, :total_harga, :metode_pembayaran, :alamat_pengiriman, 'pending', NOW())";
            $orderStmt = $this->conn->prepare($orderQuery);
            $orderStmt->bindParam(':user_id', $userId);
            $orderStmt->bindParam(':total_harga', $totalPrice);
            $orderStmt->bindParam(':metode_pembayaran', $paymentMethod);
            $orderStmt->bindParam(':alamat_pengiriman', $shippingAddress);
            $orderStmt->execute();
            
            $orderId = $this->conn->lastInsertId();
            
            // Buat detail pesanan
            $detailQuery = "INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, harga_satuan, subtotal) 
                           VALUES (:id_pesanan, :id_produk, :jumlah, :harga_satuan, :subtotal)";
            $detailStmt = $this->conn->prepare($detailQuery);
            
            foreach($items as $item) {
                $subtotal = $item['harga'] * $item['jumlah'];
                $detailStmt->bindParam(':id_pesanan', $orderId);
                $detailStmt->bindParam(':id_produk', $item['id_produk']);
                $detailStmt->bindParam(':jumlah', $item['jumlah']);
                $detailStmt->bindParam(':harga_satuan', $item['harga']);
                $detailStmt->bindParam(':subtotal', $subtotal);
                $detailStmt->execute();
                
                // Kurangi stok produk
                $this->updateProductStock($item['id_produk'], $item['jumlah']);
            }
            
            $this->conn->commit();
            return $orderId;
        } catch(Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }
    
    // Memperbarui status pesanan
    public function updateOrderStatus($orderId, $status) {
        $query = "UPDATE pesanan SET status_pesanan = :status, updated_at = NOW() WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $orderId);
        $stmt->bindParam(':status', $status);
        
        return $stmt->execute();
    }
    
    // Mendapatkan detail pesanan
    public function getOrderDetails($orderId) {
        $query = "SELECT od.*, p.nama_produk, p.gambar_produk, k.nama_kategori
                  FROM detail_pesanan od
                  JOIN produk p ON od.id_produk = p.id
                  LEFT JOIN kategori_produk k ON p.id_kategori = k.id
                  WHERE od.id_pesanan = :order_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $orderId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Mendapatkan jumlah total pesanan
    public function getTotalOrders() {
        $query = "SELECT COUNT(*) as total FROM pesanan";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    
    // Mendapatkan jumlah pesanan berdasarkan status
    public function getOrdersByStatus($status) {
        $query = "SELECT COUNT(*) as total FROM pesanan WHERE status_pesanan = :status";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    
    // Mendapatkan pesanan terbaru
    public function getRecentOrders($limit = 5) {
        $query = "SELECT o.*, u.nama_lengkap as nama_pelanggan,
                  COUNT(od.id) as jumlah_item,
                  SUM(od.subtotal) as total_harga
                  FROM pesanan o
                  LEFT JOIN users u ON o.id_user = u.id
                  LEFT JOIN detail_pesanan od ON o.id = od.id_pesanan
                  GROUP BY o.id
                  ORDER BY o.tanggal_pesanan DESC
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Fungsi untuk memperbarui stok produk setelah pesanan
    private function updateProductStock($productId, $quantity) {
        $query = "UPDATE produk SET stok = stok - :quantity WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $productId);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->execute();
    }
    
    // Mendapatkan statistik pesanan untuk dashboard
    public function getOrderStats() {
        $stats = [];
        
        // Total pesanan
        $query = "SELECT COUNT(*) as total FROM pesanan";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['total_orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Pesanan pending
        $query = "SELECT COUNT(*) as total FROM pesanan WHERE status_pesanan = 'pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['pending_orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Pesanan bulan ini
        $query = "SELECT COUNT(*) as total FROM pesanan WHERE MONTH(tanggal_pesanan) = MONTH(CURDATE()) AND YEAR(tanggal_pesanan) = YEAR(CURDATE())";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['monthly_orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Pendapatan bulan ini
        $query = "SELECT SUM(total_harga) as total FROM pesanan WHERE MONTH(tanggal_pesanan) = MONTH(CURDATE()) AND YEAR(tanggal_pesanan) = YEAR(CURDATE()) AND status_pesanan IN ('confirmed', 'processing', 'shipped', 'delivered')";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['monthly_revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        
        return $stats;
    }
}

// Fungsi helper untuk pesanan
function format_order_status($status) {
    $statusMap = [
        'pending' => ['text' => 'Pending', 'class' => 'bg-yellow-100 text-yellow-800'],
        'confirmed' => ['text' => 'Dikonfirmasi', 'class' => 'bg-blue-100 text-blue-800'],
        'processing' => ['text' => 'Diproses', 'class' => 'bg-purple-100 text-purple-800'],
        'shipped' => ['text' => 'Dikirim', 'class' => 'bg-indigo-100 text-indigo-800'],
        'delivered' => ['text' => 'Diterima', 'class' => 'bg-green-100 text-green-800'],
        'cancelled' => ['text' => 'Dibatalkan', 'class' => 'bg-red-100 text-red-800']
    ];
    
    return $statusMap[$status] ?? ['text' => ucfirst($status), 'class' => 'bg-gray-100 text-gray-800'];
}

function format_currency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function format_order_id($id) {
    return 'ORD-' . str_pad($id, 5, '0', STR_PAD_LEFT);
}
?>