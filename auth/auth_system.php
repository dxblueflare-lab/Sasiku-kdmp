<?php
// auth/auth_system.php
// Sistem autentikasi untuk ketiga role: admin, supplier, customer

session_start();

class AuthSystem {
    private $db;
    
    public function __construct($database) {
        $this->db = $database->getConnection();
    }
    
    public function login($username, $password, $role = null) {
        try {
            // Query untuk mendapatkan pengguna berdasarkan username
            $query = "SELECT * FROM users WHERE username = :username OR email = :username";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                // Jika role ditentukan, periksa apakah cocok
                if ($role && $user['role'] !== $role) {
                    return ['success' => false, 'message' => 'Akses tidak diizinkan untuk role ini'];
                }
                
                // Simpan informasi pengguna ke session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                
                return ['success' => true, 'message' => 'Login berhasil', 'user' => $user];
            } else {
                return ['success' => false, 'message' => 'Username atau password salah'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Terjadi kesalahan pada sistem'];
        }
    }
    
    public function register($username, $email, $password, $nama_lengkap, $role = 'customer') {
        try {
            // Periksa apakah username atau email sudah ada
            $checkQuery = "SELECT id FROM users WHERE username = :username OR email = :email";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->bindParam(':username', $username);
            $checkStmt->bindParam(':email', $email);
            $checkStmt->execute();
            
            if ($checkStmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Username atau email sudah digunakan'];
            }
            
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert pengguna baru
            $insertQuery = "INSERT INTO users (username, email, password, nama_lengkap, role, created_at) VALUES (:username, :email, :password, :nama_lengkap, :role, NOW())";
            $insertStmt = $this->db->prepare($insertQuery);
            $insertStmt->bindParam(':username', $username);
            $insertStmt->bindParam(':email', $email);
            $insertStmt->bindParam(':password', $hashedPassword);
            $insertStmt->bindParam(':nama_lengkap', $nama_lengkap);
            $insertStmt->bindParam(':role', $role);
            
            if ($insertStmt->execute()) {
                return ['success' => true, 'message' => 'Registrasi berhasil'];
            } else {
                return ['success' => false, 'message' => 'Gagal melakukan registrasi'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Terjadi kesalahan pada sistem'];
        }
    }
    
    public function logout() {
        session_destroy();
        return ['success' => true, 'message' => 'Logout berhasil'];
    }
    
    public function getCurrentUser() {
        if (isset($_SESSION['user_id'])) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'role' => $_SESSION['role'],
                'nama_lengkap' => $_SESSION['nama_lengkap']
            ];
        }
        return null;
    }
    
    public function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.html');
            exit();
        }
    }
    
    public function requireRole($allowedRoles) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.html');
            exit();
        }
        
        if (!in_array($_SESSION['role'], $allowedRoles)) {
            header('HTTP/1.1 403 Forbidden');
            die('Akses ditolak: Anda tidak memiliki izin untuk mengakses halaman ini.');
        }
    }
}

// Fungsi helper untuk redirect berdasarkan role
function redirectToDashboard($role) {
    switch($role) {
        case 'admin':
            header('Location: ../admin/dashboard.html');
            break;
        case 'supplier':
            header('Location: ../supplier/dashboard.html');
            break;
        case 'customer':
            header('Location: ../customer/dashboard.html');
            break;
        default:
            header('Location: ../auth/login.html');
            break;
    }
    exit();
}
?>