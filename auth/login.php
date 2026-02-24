<?php
// auth/login.php
// File untuk menangani proses login pengguna

require_once '../config/config.php';
require_once 'auth.php';

// Jika pengguna sudah login, redirect ke dashboard sesuai role
if (is_logged_in()) {
    $currentUser = current_user();
    redirectToDashboard($currentUser['role']);
    exit();
}

$error_message = '';

// Proses login jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $loginResult = auth()->login($username, $password);
    
    if ($loginResult['success']) {
        // Redirect ke dashboard sesuai role
        $currentUser = $loginResult['user'];
        redirectToDashboard($currentUser['role']);
        exit();
    } else {
        $error_message = $loginResult['message'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dapur Suplai</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        merah: '#DC2626',
                        putih: '#FFFFFF',
                        emas: '#F59E0B',
                        gelap: '#1F2937',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background: linear-gradient(135deg, #DC2626 0%, #991B1B 50%, #7F1D1D 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body>
    <div class="container mx-auto px-4 py-12 max-w-md">
        <div class="login-card p-8">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-gradient-to-br from-merah to-red-800 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                     <img src="https://www.appdapursuplai.org/images/logo.png" alt="Logo" width="50" height="50">
                </div>
                <h1 class="text-2xl font-bold text-merah">DAPUR SUPLAI</h1>
                <p class="text-gray-600">Silakan masuk ke akun Anda</p>
            </div>

            <form method="POST" action="">
                <?php if (!empty($error_message)): ?>
                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
                <?php endif; ?>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                        Username atau Email
                    </label>
                    <input type="text" id="username" name="username" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-merah" placeholder="Masukkan username atau email" required>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                        Kata Sandi
                    </label>
                    <input type="password" id="password" name="password" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-merah" placeholder="••••••••" required>
                </div>

                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="remember" type="checkbox" class="w-4 h-4 text-merah bg-gray-100 border-gray-300 rounded focus:ring-merah">
                        </div>
                        <label for="remember" class="ml-2 text-sm text-gray-600">Ingat saya</label>
                    </div>
                    <a href="#" class="text-sm text-merah hover:underline">Lupa kata sandi?</a>
                </div>

                <button type="submit" class="w-full bg-merah text-white py-3 rounded-xl font-bold hover:bg-red-700 transition shadow-lg">
                    Masuk
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-600 text-sm">
                    Belum punya akun? 
                    <a href="register.php" class="text-merah font-semibold hover:underline">Daftar di sini</a>
                </p>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="flex justify-center space-x-6">
                    <a href="#" class="text-gray-500 hover:text-merah">
                        <i class="fab fa-facebook-f text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-merah">
                        <i class="fab fa-google text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-merah">
                        <i class="fab fa-twitter text-xl"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-6">
            <a href="../index.html" class="text-white hover:text-emas flex items-center justify-center">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</body>
</html>