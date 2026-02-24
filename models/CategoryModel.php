<?php
// models/CategoryModel.php
// Model untuk mengelola kategori produk dalam sistem Dapur Suplai

class CategoryModel {
    private $conn;
    
    public function __construct($database) {
        $this->conn = $database->getConnection();
    }
    
    // Mendapatkan semua kategori
    public function getAllCategories() {
        $query = "SELECT * FROM kategori_produk ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Mendapatkan kategori berdasarkan ID
    public function getCategoryById($id) {
        $query = "SELECT * FROM kategori_produk WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Mendapatkan kategori dengan jumlah produk
    public function getCategoriesWithProductCount() {
        $query = "SELECT 
                    k.*,
                    COUNT(p.id) as jumlah_produk
                  FROM kategori_produk k
                  LEFT JOIN produk p ON k.id = p.id_kategori
                  GROUP BY k.id
                  ORDER BY k.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Membuat kategori baru
    public function createCategory($data) {
        $query = "INSERT INTO kategori_produk (nama_kategori, deskripsi, created_at) VALUES (:name, :description, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        
        return $stmt->execute();
    }
    
    // Memperbarui kategori
    public function updateCategory($id, $data) {
        $query = "UPDATE kategori_produk SET nama_kategori = :name, deskripsi = :description, updated_at = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        
        return $stmt->execute();
    }
    
    // Menghapus kategori
    public function deleteCategory($id) {
        $query = "DELETE FROM kategori_produk WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    // Mendapatkan produk berdasarkan kategori
    public function getProductsByCategory($categoryId, $limit = 12, $offset = 0) {
        $query = "SELECT 
                    p.*,
                    u.nama_lengkap as nama_supplier,
                    k.nama_kategori
                  FROM produk p
                  JOIN users u ON p.id_supplier = u.id
                  JOIN kategori_produk k ON p.id_kategori = k.id
                  WHERE p.id_kategori = :category_id
                  ORDER BY p.created_at DESC
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $categoryId);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Mendapatkan jumlah produk dalam kategori
    public function getProductCountByCategory($categoryId) {
        $query = "SELECT COUNT(*) as total FROM produk WHERE id_kategori = :category_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $categoryId);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    
    // Mencari kategori
    public function searchCategories($keyword) {
        $query = "SELECT * FROM kategori_produk WHERE nama_kategori LIKE :keyword OR deskripsi LIKE :keyword ORDER BY created_at DESC";
        $searchTerm = '%' . $keyword . '%';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':keyword', $searchTerm);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Mendapatkan kategori populer (berdasarkan jumlah produk)
    public function getPopularCategories($limit = 5) {
        $query = "SELECT 
                    k.*,
                    COUNT(p.id) as jumlah_produk
                  FROM kategori_produk k
                  LEFT JOIN produk p ON k.id = p.id_kategori
                  GROUP BY k.id
                  ORDER BY jumlah_produk DESC
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Mengecek apakah kategori memiliki produk
    public function hasProducts($categoryId) {
        $query = "SELECT COUNT(*) as count FROM produk WHERE id_kategori = :category_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $categoryId);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
    
    // Mendapatkan kategori untuk dropdown
    public function getCategoryOptions() {
        $categories = $this->getAllCategories();
        $options = [];
        
        foreach($categories as $category) {
            $options[] = [
                'value' => $category['id'],
                'label' => $category['nama_kategori']
            ];
        }
        
        return $options;
    }
}

// Fungsi helper untuk kategori
function format_category_description($description, $maxLength = 100) {
    if (strlen($description) > $maxLength) {
        return substr($description, 0, $maxLength) . '...';
    }
    return $description;
}

function get_category_icon($categoryName) {
    $iconMap = [
        'Sayur-Sayuran' => 'fa-carrot',
        'Buah-Buahan' => 'fa-apple-alt',
        'Sembako' => 'fa-boxes',
        'Protein Hewani' => 'fa-drumstick-bite',
        'Protein Nabati' => 'fa-seedling',
        'Minyak dan Bumbu' => 'fa-blender',
        'Makanan Olahan' => 'fa-cookie-bite',
        'Minuman' => 'fa-glass-whiskey',
        'Alat Dapur' => 'fa-utensils'
    ];
    
    return $iconMap[$categoryName] ?? 'fa-box';
}

function validate_category_data($data) {
    $errors = [];
    
    if (empty($data['name'])) {
        $errors[] = 'Nama kategori harus diisi';
    }
    
    if (strlen($data['name']) > 100) {
        $errors[] = 'Nama kategori maksimal 100 karakter';
    }
    
    if (strlen($data['description'] ?? '') > 500) {
        $errors[] = 'Deskripsi kategori maksimal 500 karakter';
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}
?>