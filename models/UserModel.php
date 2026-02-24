<?php
// models/UserModel.php
// Model untuk mengelola data pengguna dalam sistem Dapur Suplai

class UserModel {
    private $conn;
    
    public function __construct($database) {
        $this->conn = $database->getConnection();
    }
    
    // Mendapatkan semua pengguna
    public function getAllUsers($limit = 10, $offset = 0) {
        $query = "SELECT u.*, r.name as role_name
                  FROM users u
                  LEFT JOIN model_has_roles mhr ON u.id = mhr.model_id
                  LEFT JOIN roles r ON mhr.role_id = r.id
                  ORDER BY u.created_at DESC
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Mendapatkan pengguna berdasarkan ID
    public function getUserById($id) {
        $query = "SELECT u.*, r.name as role_name
                  FROM users u
                  LEFT JOIN model_has_roles mhr ON u.id = mhr.model_id
                  LEFT JOIN roles r ON mhr.role_id = r.id
                  WHERE u.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Mendapatkan pengguna berdasarkan role
    public function getUsersByRole($role) {
        $query = "SELECT u.*, r.name as role_name
                  FROM users u
                  JOIN model_has_roles mhr ON u.id = mhr.model_id
                  JOIN roles r ON mhr.role_id = r.id
                  WHERE r.name = :role
                  ORDER BY u.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':role', $role);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Mendaftarkan pengguna baru
    public function registerUser($userData) {
        try {
            $this->conn->beginTransaction();
            
            // Hash password
            $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
            
            // Insert user baru
            $query = "INSERT INTO users (username, email, password, nama_lengkap, alamat, nomor_telepon, created_at) 
                      VALUES (:username, :email, :password, :nama_lengkap, :alamat, :nomor_telepon, NOW())";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $userData['username']);
            $stmt->bindParam(':email', $userData['email']);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':nama_lengkap', $userData['nama_lengkap']);
            $stmt->bindParam(':alamat', $userData['alamat']);
            $stmt->bindParam(':nomor_telepon', $userData['nomor_telepon']);
            $stmt->execute();
            
            $userId = $this->conn->lastInsertId();
            
            // Assign role berdasarkan tipe pengguna
            $role = $userData['role'] ?? 'customer';
            $this->assignRoleToUser($userId, $role);
            
            $this->conn->commit();
            return $userId;
        } catch(Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }
    
    // Menetapkan role ke pengguna
    private function assignRoleToUser($userId, $roleName) {
        // Dapatkan ID role
        $roleQuery = "SELECT id FROM roles WHERE name = :role_name";
        $roleStmt = $this->conn->prepare($roleQuery);
        $roleStmt->bindParam(':role_name', $roleName);
        $roleStmt->execute();
        $role = $roleStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($role) {
            $assignQuery = "INSERT INTO model_has_roles (role_id, model_type, model_id) 
                           VALUES (:role_id, 'App\\Models\\User', :model_id)";
            $assignStmt = $this->conn->prepare($assignQuery);
            $assignStmt->bindParam(':role_id', $role['id']);
            $assignStmt->bindParam(':model_id', $userId);
            $assignStmt->execute();
        }
    }
    
    // Memperbarui informasi pengguna
    public function updateUser($id, $userData) {
        $query = "UPDATE users SET 
                  username = :username,
                  email = :email,
                  nama_lengkap = :nama_lengkap,
                  alamat = :alamat,
                  nomor_telepon = :nomor_telepon,
                  updated_at = NOW()
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':username', $userData['username']);
        $stmt->bindParam(':email', $userData['email']);
        $stmt->bindParam(':nama_lengkap', $userData['nama_lengkap']);
        $stmt->bindParam(':alamat', $userData['alamat']);
        $stmt->bindParam(':nomor_telepon', $userData['nomor_telepon']);
        
        return $stmt->execute();
    }
    
    // Menghapus pengguna
    public function deleteUser($id) {
        $query = "DELETE FROM users WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    // Mendapatkan jumlah total pengguna
    public function getTotalUsers() {
        $query = "SELECT COUNT(*) as total FROM users";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    
    // Mendapatkan jumlah pengguna berdasarkan role
    public function getTotalUsersByRole($role) {
        $query = "SELECT COUNT(*) as total 
                  FROM users u
                  JOIN model_has_roles mhr ON u.id = mhr.model_id
                  JOIN roles r ON mhr.role_id = r.id
                  WHERE r.name = :role";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':role', $role);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    
    // Mencari pengguna
    public function searchUsers($keyword) {
        $query = "SELECT u.*, r.name as role_name
                  FROM users u
                  LEFT JOIN model_has_roles mhr ON u.id = mhr.model_id
                  LEFT JOIN roles r ON mhr.role_id = r.id
                  WHERE u.username LIKE :keyword 
                  OR u.email LIKE :keyword 
                  OR u.nama_lengkap LIKE :keyword
                  ORDER BY u.created_at DESC";
        
        $searchTerm = '%' . $keyword . '%';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':keyword', $searchTerm);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Mendapatkan statistik pengguna untuk dashboard
    public function getUserStats() {
        $stats = [];
        
        // Total pengguna
        $query = "SELECT COUNT(*) as total FROM users";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Pengguna aktif bulan ini
        $query = "SELECT COUNT(*) as total FROM users WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['monthly_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Jumlah admin
        $stats['admin_count'] = $this->getTotalUsersByRole('admin');
        
        // Jumlah supplier
        $stats['supplier_count'] = $this->getTotalUsersByRole('supplier');
        
        // Jumlah customer
        $stats['customer_count'] = $this->getTotalUsersByRole('customer');
        
        return $stats;
    }
    
    // Fungsi untuk login
    public function authenticate($username, $password) {
        $query = "SELECT u.*, r.name as role_name
                  FROM users u
                  LEFT JOIN model_has_roles mhr ON u.id = mhr.model_id
                  LEFT JOIN roles r ON mhr.role_id = r.id
                  WHERE u.username = :username OR u.email = :username";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            // Hapus password dari data yang dikembalikan
            unset($user['password']);
            return $user;
        }
        
        return false;
    }
}

// Fungsi helper untuk pengguna
function format_user_role($role) {
    $roleLabels = [
        'admin' => ['label' => 'Administrator', 'color' => 'bg-red-100 text-red-800'],
        'supplier' => ['label' => 'Supplier', 'color' => 'bg-blue-100 text-blue-800'],
        'customer' => ['label' => 'Customer', 'color' => 'bg-green-100 text-green-800']
    ];
    
    return $roleLabels[$role] ?? ['label' => ucfirst($role), 'color' => 'bg-gray-100 text-gray-800'];
}

function format_phone_number($phone) {
    // Format nomor telepon ke format Indonesia
    if (substr($phone, 0, 1) === '0') {
        return '+62' . substr($phone, 1);
    }
    return $phone;
}
?>