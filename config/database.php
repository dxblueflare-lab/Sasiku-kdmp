<?php
// config/database.php
// File konfigurasi koneksi database untuk sistem Dapur Suplai

class Database {
    private $host = 'localhost';
    private $db_name = 'ecommerce_dapur_mbg';
    private $username = 'root';
    private $password = '';
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4", 
                                  $this->username, 
                                  $this->password,
                                  array(
                                      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                                      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                                      PDO::ATTR_EMULATE_PREPARES => false,
                                  ));
        } catch(PDOException $exception) {
            echo "Koneksi database gagal: " . $exception->getMessage();
        }
        return $this->conn;
    }
}

// Fungsi untuk memeriksa apakah pengguna sudah login
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Fungsi untuk mendapatkan informasi pengguna yang login
function getCurrentUser() {
    if (isset($_SESSION['user_id'])) {
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'role' => $_SESSION['role']
        ];
    }
    return null;
}

// Fungsi untuk memeriksa role pengguna
function hasRole($role) {
    if (!isLoggedIn()) {
        return false;
    }
    return $_SESSION['role'] === $role;
}

// Fungsi untuk redirect jika tidak memiliki akses
function requireRole($role) {
    if (!hasRole($role)) {
        header('Location: ../auth/login.html');
        exit();
    }
}

// Mulai session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>