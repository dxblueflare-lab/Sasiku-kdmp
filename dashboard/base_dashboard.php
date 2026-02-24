<?php
// dashboard/base_dashboard.php
// Base template for dashboard pages

require_once __DIR__ . '/../includes/DatabaseConfig.php';
require_once __DIR__ . '/../includes/DatabaseStorage.php';
require_once __DIR__ . '/../includes/DatabaseCRUD.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit();
}

$user = $_SESSION['user'];

// Check if user has the correct role for this dashboard
if ($user['role'] !== basename(dirname($_SERVER['PHP_SELF']))) {
    header('Location: ../index.php');
    exit();
}

// Common dashboard functions
function get_user_stats($userId) {
    try {
        $crud = new DatabaseCRUD();
        
        // Get user's order statistics
        $orders = $crud->count('pesanan', ['id_user' => $userId]);
        $completed_orders = $crud->count('pesanan', ['id_user' => $userId, 'status_pesanan' => 'delivered']);
        $pending_orders = $crud->count('pesanan', ['id_user' => $userId, 'status_pesanan' => 'pending']);
        
        return [
            'total_orders' => $orders['success'] ? $orders['count'] : 0,
            'completed_orders' => $completed_orders['success'] ? $completed_orders['count'] : 0,
            'pending_orders' => $pending_orders['success'] ? $pending_orders['count'] : 0
        ];
    } catch (Exception $e) {
        return [
            'total_orders' => 0,
            'completed_orders' => 0,
            'pending_orders' => 0
        ];
    }
}

function format_currency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}
?>