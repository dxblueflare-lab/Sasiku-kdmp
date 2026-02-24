<?php
// auth/session_manager.php
// File ini mengelola session untuk sistem autentikasi

class SessionManager {
    private static $instance = null;
    private $sessionStarted = false;
    
    private function __construct() {
        $this->startSession();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            // Konfigurasi session
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_strict_mode', 1);
            ini_set('session.cookie_secure', 0); // Set ke 1 jika menggunakan HTTPS
            
            session_start();
            $this->sessionStarted = true;
        }
    }
    
    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    public function get($key) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }
    
    public function has($key) {
        return isset($_SESSION[$key]);
    }
    
    public function remove($key) {
        unset($_SESSION[$key]);
    }
    
    public function destroy() {
        session_unset();
        session_destroy();
        session_regenerate_id(true); // Regenerasi session ID untuk keamanan
    }
    
    public function regenerateId() {
        session_regenerate_id(true);
    }
    
    public function isLoggedIn() {
        return $this->has('user_id') && $this->has('role');
    }
    
    public function getUserRole() {
        return $this->get('role');
    }
    
    public function getUserId() {
        return $this->get('user_id');
    }
    
    public function getUsername() {
        return $this->get('username');
    }
    
    public function getUserInfo() {
        if ($this->isLoggedIn()) {
            return [
                'id' => $this->getUserId(),
                'username' => $this->getUsername(),
                'role' => $this->getUserRole(),
                'nama_lengkap' => $this->get('nama_lengkap')
            ];
        }
        return null;
    }
    
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            $this->destroy();
            header('Location: ../auth/login.html');
            exit();
        }
    }
    
    public function requireRole($allowedRoles) {
        $this->requireLogin();
        
        $userRole = $this->getUserRole();
        if (!in_array($userRole, $allowedRoles)) {
            // Jika role tidak diizinkan, redirect ke halaman sesuai role
            switch($userRole) {
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
    }
    
    public function validateSession() {
        if ($this->isLoggedIn()) {
            // Validasi session (misalnya: cek IP, user agent, dll)
            // Ini opsional untuk keamanan tambahan
            return true;
        }
        return false;
    }
}

// Fungsi helper untuk akses cepat ke session manager
function session_manager() {
    return SessionManager::getInstance();
}

// Fungsi helper untuk cek login
function is_logged_in() {
    return session_manager()->isLoggedIn();
}

// Fungsi helper untuk cek role
function has_role($role) {
    $session = session_manager();
    return $session->isLoggedIn() && $session->getUserRole() === $role;
}

// Fungsi helper untuk redirect berdasarkan role
function redirect_by_role() {
    $session = session_manager();
    if ($session->isLoggedIn()) {
        $role = $session->getUserRole();
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
    } else {
        header('Location: ../auth/login.html');
        exit();
    }
}

// Fungsi untuk logout
function perform_logout() {
    session_manager()->destroy();
    header('Location: ../auth/login.html');
    exit();
}
?>