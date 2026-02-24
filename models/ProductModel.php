<?php
// models/ProductModel.php
// Model untuk mengelola data produk dalam sistem Dapur Suplai

class ProductModel {
    private $conn;
    
    public function __construct($database) {
        $this->conn = $database->getConnection();
    }
    
    // Mendapatkan semua produk
    public function getAllProducts($limit = 10, $offset = 0) {
        $query = "SELECT p.*, k.nama_kategori, u.nama_lengkap as nama_supplier 
                  FROM produk p 
                  LEFT JOIN kategori_produk k ON p.id_kategori = k.id 
                  LEFT JOIN users u ON p.id_supplier = u.id 
                  ORDER BY p.created_at DESC 
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Mendapatkan produk berdasarkan ID
    public function getProductById($id) {
        $query = "SELECT p.*, k.nama_kategori, u.nama_lengkap as nama_supplier 
                  FROM produk p 
                  LEFT JOIN kategori_produk k ON p.id_kategori = k.id 
                  LEFT JOIN users u ON p.id_supplier = u.id 
                  WHERE p.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Mendapatkan produk berdasarkan supplier
    public function getProductsBySupplier($supplierId) {
        $query = "SELECT p.*, k.nama_kategori 
                  FROM produk p 
                  LEFT JOIN kategori_produk k ON p.id_kategori = k.id 
                  WHERE p.id_supplier = :supplier_id 
                  ORDER BY p.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':supplier_id', $supplierId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Mencari produk
    public function searchProducts($keyword) {
        $query = "SELECT p.*, k.nama_kategori, u.nama_lengkap as nama_supplier 
                  FROM produk p 
                  LEFT JOIN kategori_produk k ON p.id_kategori = k.id 
                  LEFT JOIN users u ON p.id_supplier = u.id 
                  WHERE p.nama_produk LIKE :keyword OR p.deskripsi LIKE :keyword
                  ORDER BY p.created_at DESC";
        
        $searchTerm = '%' . $keyword . '%';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':keyword', $searchTerm);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Mendapatkan produk berdasarkan kategori
    public function getProductsByCategory($categoryId) {
        $query = "SELECT p.*, k.nama_kategori, u.nama_lengkap as nama_supplier 
                  FROM produk p 
                  LEFT JOIN kategori_produk k ON p.id_kategori = k.id 
                  LEFT JOIN users u ON p.id_supplier = u.id 
                  WHERE p.id_kategori = :category_id 
                  ORDER BY p.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $categoryId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Menambah produk baru
    public function createProduct($data) {
        $query = "INSERT INTO produk (nama_produk, deskripsi, harga, stok, id_kategori, id_supplier, gambar_produk, created_at) 
                  VALUES (:nama_produk, :deskripsi, :harga, :stok, :id_kategori, :id_supplier, :gambar_produk, NOW())";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nama_produk', $data['nama_produk']);
        $stmt->bindParam(':deskripsi', $data['deskripsi']);
        $stmt->bindParam(':harga', $data['harga']);
        $stmt->bindParam(':stok', $data['stok']);
        $stmt->bindParam(':id_kategori', $data['id_kategori']);
        $stmt->bindParam(':id_supplier', $data['id_supplier']);
        $stmt->bindParam(':gambar_produk', $data['gambar_produk']);
        
        return $stmt->execute();
    }
    
    // Memperbarui produk
    public function updateProduct($id, $data) {
        $query = "UPDATE produk SET 
                  nama_produk = :nama_produk, 
                  deskripsi = :deskripsi, 
                  harga = :harga, 
                  stok = :stok, 
                  id_kategori = :id_kategori, 
                  gambar_produk = :gambar_produk 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nama_produk', $data['nama_produk']);
        $stmt->bindParam(':deskripsi', $data['deskripsi']);
        $stmt->bindParam(':harga', $data['harga']);
        $stmt->bindParam(':stok', $data['stok']);
        $stmt->bindParam(':id_kategori', $data['id_kategori']);
        $stmt->bindParam(':gambar_produk', $data['gambar_produk']);
        
        return $stmt->execute();
    }
    
    // Menghapus produk
    public function deleteProduct($id) {
        $query = "DELETE FROM produk WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    // Mendapatkan jumlah total produk
    public function getTotalProducts() {
        $query = "SELECT COUNT(*) as total FROM produk";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    
    // Mendapatkan jumlah produk berdasarkan supplier
    public function getTotalProductsBySupplier($supplierId) {
        $query = "SELECT COUNT(*) as total FROM produk WHERE id_supplier = :supplier_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':supplier_id', $supplierId);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    
    // Mendapatkan produk terlaris
    public function getBestSellingProducts($limit = 5) {
        $query = "SELECT p.*, k.nama_kategori, u.nama_lengkap as nama_supplier,
                  SUM(dp.jumlah) as total_terjual
                  FROM produk p
                  LEFT JOIN kategori_produk k ON p.id_kategori = k.id
                  LEFT JOIN users u ON p.id_supplier = u.id
                  LEFT JOIN detail_pesanan dp ON p.id = dp.id_produk
                  GROUP BY p.id
                  ORDER BY total_terjual DESC
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Mendapatkan produk dengan stok rendah
    public function getLowStockProducts($threshold = 10) {
        $query = "SELECT p.*, k.nama_kategori, u.nama_lengkap as nama_supplier
                  FROM produk p
                  LEFT JOIN kategori_produk k ON p.id_kategori = k.id
                  LEFT JOIN users u ON p.id_supplier = u.id
                  WHERE p.stok <= :threshold AND p.stok > 0
                  ORDER BY p.stok ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':threshold', $threshold);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Fungsi helper untuk produk
function format_currency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function truncate_text($text, $length = 50) {
    if (strlen($text) > $length) {
        return substr($text, 0, $length) . '...';
    }
    return $text;
}
?>