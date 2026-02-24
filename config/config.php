<?php
// config/config.php
// File konfigurasi utama sistem Dapur Suplai

// Konfigurasi database
define('DB_HOST', 'localhost');
define('DB_NAME', 'ecommerce_dapur_mbg');
define('DB_USER', 'root');
define('DB_PASS', '');

// Konfigurasi aplikasi
define('APP_NAME', 'Dapur Suplai MBG');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/kdmp');

// Konfigurasi session
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_secure', 0); // Set ke 1 jika menggunakan HTTPS

// Fungsi untuk koneksi database
function getDBConnection() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", 
                      DB_USER, 
                      DB_PASS,
                      array(
                          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                          PDO::ATTR_EMULATE_PREPARES => false,
                      ));
        return $pdo;
    } catch(PDOException $e) {
        die("Koneksi database gagal: " . $e->getMessage());
    }
}

// Fungsi untuk redirect ke halaman login jika belum login
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: auth/login.php');
        exit();
    }
}

// Fungsi untuk redirect ke halaman berdasarkan role
function redirectToDashboard($role) {
    switch($role) {
        case 'admin':
            header('Location: admin/dashboard.html');
            break;
        case 'supplier':
            header('Location: supplier/dashboard.html');
            break;
        case 'customer':
            header('Location: customer/dashboard.html');
            break;
        default:
            header('Location: auth/login.php');
            break;
    }
    exit();
}

// Fungsi untuk cek role pengguna
function hasRole($requiredRole) {
    if (!isset($_SESSION['role'])) {
        return false;
    }
    return $_SESSION['role'] === $requiredRole;
}

// Fungsi untuk cek apakah pengguna login
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Fungsi untuk logout
function performLogout() {
    // Periksa apakah session sudah dimulai sebelumnya
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
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

    return array('success' => true);
}

// Fungsi untuk sanitasi input
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

// Fungsi untuk format mata uang
function formatCurrency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

// Fungsi untuk format tanggal
function formatDate($date) {
    $dateObj = new DateTime($date);
    return $dateObj->format('d M Y');
}

// Mulai session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>