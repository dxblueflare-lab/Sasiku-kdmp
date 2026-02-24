<?php
// models/CartModel.php
// Model untuk mengelola keranjang belanja dalam sistem Dapur Suplai

class CartModel {
    private $conn;
    
    public function __construct($database) {
        $this->conn = $database->getConnection();
    }
    
    // Menambahkan item ke keranjang
    public function addItem($userId, $productId, $quantity = 1) {
        // Cek apakah produk sudah ada di keranjang
        $query = "SELECT id, jumlah FROM keranjang WHERE id_user = :user_id AND id_produk = :product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);
        $stmt->execute();
        
        $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingItem) {
            // Jika sudah ada, tambahkan jumlahnya
            $newQuantity = $existingItem['jumlah'] + $quantity;
            $updateQuery = "UPDATE keranjang SET jumlah = :quantity WHERE id = :item_id";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bindParam(':quantity', $newQuantity);
            $updateStmt->bindParam(':item_id', $existingItem['id']);
            return $updateStmt->execute();
        } else {
            // Jika belum ada, buat item baru
            $insertQuery = "INSERT INTO keranjang (id_user, id_produk, jumlah, created_at) VALUES (:user_id, :product_id, :quantity, NOW())";
            $insertStmt = $this->conn->prepare($insertQuery);
            $insertStmt->bindParam(':user_id', $userId);
            $insertStmt->bindParam(':product_id', $productId);
            $insertStmt->bindParam(':quantity', $quantity);
            return $insertStmt->execute();
        }
    }
    
    // Mendapatkan semua item di keranjang pengguna
    public function getUserCart($userId) {
        $query = "SELECT 
                    k.*,
                    p.nama_produk,
                    p.harga,
                    p.gambar_produk,
                    k.jumlah * p.harga as subtotal
                  FROM keranjang k
                  JOIN produk p ON k.id_produk = p.id
                  WHERE k.id_user = :user_id
                  ORDER BY k.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Memperbarui jumlah item di keranjang
    public function updateItemQuantity($cartId, $quantity) {
        if ($quantity <= 0) {
            return $this->removeItem($cartId);
        }
        
        $query = "UPDATE keranjang SET jumlah = :quantity WHERE id = :cart_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':cart_id', $cartId);
        
        return $stmt->execute();
    }
    
    // Menghapus item dari keranjang
    public function removeItem($cartId) {
        $query = "DELETE FROM keranjang WHERE id = :cart_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cart_id', $cartId);
        
        return $stmt->execute();
    }
    
    // Menghapus semua item dari keranjang
    public function clearCart($userId) {
        $query = "DELETE FROM keranjang WHERE id_user = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        
        return $stmt->execute();
    }
    
    // Mendapatkan jumlah total item di keranjang
    public function getCartItemCount($userId) {
        $query = "SELECT SUM(jumlah) as total_items FROM keranjang WHERE id_user = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_items'] ? $result['total_items'] : 0;
    }
    
    // Mendapatkan total harga keranjang
    public function getCartTotal($userId) {
        $query = "SELECT SUM(k.jumlah * p.harga) as total 
                  FROM keranjang k
                  JOIN produk p ON k.id_produk = p.id
                  WHERE k.id_user = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ? $result['total'] : 0;
    }
    
    // Memeriksa apakah produk sudah ada di keranjang
    public function isProductInCart($userId, $productId) {
        $query = "SELECT COUNT(*) as count FROM keranjang WHERE id_user = :user_id AND id_produk = :product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
    
    // Mendapatkan detail produk untuk checkout
    public function getCartForCheckout($userId) {
        $query = "SELECT 
                    k.id as cart_item_id,
                    k.jumlah,
                    p.id as product_id,
                    p.nama_produk,
                    p.harga,
                    p.gambar_produk,
                    p.stok,
                    (k.jumlah * p.harga) as subtotal
                  FROM keranjang k
                  JOIN produk p ON k.id_produk = p.id
                  WHERE k.id_user = :user_id
                  ORDER BY k.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Memvalidasi ketersediaan stok sebelum checkout
    public function validateCartStock($userId) {
        $cartItems = $this->getCartForCheckout($userId);
        $errors = [];
        
        foreach($cartItems as $item) {
            if($item['jumlah'] > $item['stok']) {
                $errors[] = [
                    'product_name' => $item['nama_produk'],
                    'requested_qty' => $item['jumlah'],
                    'available_qty' => $item['stok']
                ];
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    // Menghitung berat total keranjang (jika sistem pengiriman memerlukan)
    public function getCartWeight($userId) {
        $query = "SELECT SUM(k.jumlah * COALESCE(p.berat, 0)) as total_weight
                  FROM keranjang k
                  JOIN produk p ON k.id_produk = p.id
                  WHERE k.id_user = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_weight'] ? $result['total_weight'] : 0;
    }
}

// Fungsi helper untuk keranjang
function format_cart_total($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function validate_product_availability($productId, $quantity) {
    global $pdo; // Asumsikan koneksi database tersedia
    
    $query = "SELECT stok FROM produk WHERE id = :product_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':product_id', $productId);
    $stmt->execute();
    
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        return ['available' => false, 'message' => 'Produk tidak ditemukan'];
    }
    
    if ($product['stok'] < $quantity) {
        return ['available' => false, 'message' => 'Stok tidak mencukupi. Tersedia: ' . $product['stok']];
    }
    
    return ['available' => true, 'message' => 'Produk tersedia'];
}

// Fungsi untuk menghitung estimasi biaya pengiriman (opsional)
function calculateShippingCost($weight, $destination) {
    // Dalam implementasi nyata, ini akan menghitung biaya pengiriman berdasarkan berat dan tujuan
    // Untuk simulasi, kita gunakan biaya flat
    return 15000; // Rp 15.000 untuk pengiriman
}
?>