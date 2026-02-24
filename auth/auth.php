<?php
// auth/auth.php
// File untuk mengelola autentikasi pengguna

require_once '../config/config.php';

class Auth {
    private $pdo;
    
    public function __construct() {
        $this->pdo = getDBConnection();
    }
    
    // Fungsi login
    public function login($username, $password) {
        try {
            // Cek apakah username/email cocok
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Simpan informasi pengguna ke session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                
                return [
                    'success' => true,
                    'message' => 'Login berhasil',
                    'user' => [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'role' => $user['role'],
                        'nama_lengkap' => $user['nama_lengkap']
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Username atau password salah'
                ];
            }
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem'
            ];
        }
    }
    
    // Fungsi register
    public function register($userData) {
        try {
            // Cek apakah username atau email sudah ada
            $checkStmt = $this->pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $checkStmt->execute([$userData['username'], $userData['email']]);
            
            if ($checkStmt->rowCount() > 0) {
                return [
                    'success' => false,
                    'message' => 'Username atau email sudah digunakan'
                ];
            }
            
            // Hash password
            $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
            
            // Insert pengguna baru
            $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password, nama_lengkap, alamat, nomor_telepon, role, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
            $result = $stmt->execute([
                $userData['username'],
                $userData['email'],
                $hashedPassword,
                $userData['nama_lengkap'],
                $userData['alamat'] ?? '',
                $userData['nomor_telepon'] ?? '',
                $userData['role'] ?? 'customer'
            ]);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Registrasi berhasil'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Gagal melakukan registrasi'
                ];
            }
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ];
        }
    }
    
    // Fungsi logout
    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        
        // Hapus cookie session jika ada
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        return [
            'success' => true,
            'message' => 'Logout berhasil'
        ];
    }
    
    // Fungsi untuk cek apakah pengguna sudah login
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    // Fungsi untuk mendapatkan informasi pengguna yang sedang login
    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'role' => $_SESSION['role'],
                'nama_lengkap' => $_SESSION['nama_lengkap']
            ];
        }
        return null;
    }
    
    // Fungsi untuk cek role pengguna
    public function hasRole($role) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        return $_SESSION['role'] === $role;
    }
    
    // Fungsi untuk redirect berdasarkan role
    public function redirectToDashboard() {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit();
        }
        
        $role = $_SESSION['role'];
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
                header('Location: auth/login.php');
                break;
        }
        exit();
    }
    
    // Fungsi untuk cek akses berdasarkan role
    public function requireRole($allowedRoles) {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit();
        }
        
        $currentRole = $_SESSION['role'];
        if (!in_array($currentRole, $allowedRoles)) {
            // Jika role tidak diizinkan, redirect ke halaman sesuai role
            $this->redirectToDashboard();
        }
    }
}

// Fungsi helper untuk autentikasi
function auth() {
    static $authInstance = null;
    if ($authInstance === null) {
        $authInstance = new Auth();
    }
    return $authInstance;
}

// Fungsi untuk cek apakah pengguna login
function is_logged_in() {
    return auth()->isLoggedIn();
}

// Fungsi untuk cek role
function has_role($role) {
    return auth()->hasRole($role);
}

// Fungsi untuk mendapatkan user saat ini
function current_user() {
    return auth()->getCurrentUser();
}

// Fungsi untuk redirect ke dashboard
function redirect_to_dashboard() {
    auth()->redirectToDashboard();
}
?>