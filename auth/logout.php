<?php
// auth/logout.php
// File untuk menangani proses logout pengguna

require_once '../config/config.php';

// Lakukan proses logout
performLogout();

// Setelah logout, redirect ke halaman login
header('Location: login.php?logged_out=1');
exit();
?>