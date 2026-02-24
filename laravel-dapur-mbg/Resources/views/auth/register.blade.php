<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Dapur Suplai</title>
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
        .register-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
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
        <div class="register-card p-8">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-gradient-to-br from-merah to-red-800 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <img src="https://www.appdapursuplai.org/images/logo.png" alt="Logo" width="50" height="50">
                </div>
                <h1 class="text-2xl font-bold text-merah">DAPUR SUPLAI</h1>
                <p class="text-gray-600">Buat akun baru Anda</p>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                        Username
                    </label>
                    <input type="text" id="username" name="username" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-merah focus:border-transparent" placeholder="Masukkan username" value="{{ old('username') }}" required>
                    @error('username')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                        Email
                    </label>
                    <input type="email" id="email" name="email" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-merah focus:border-transparent" placeholder="Masukkan email" value="{{ old('email') }}" required>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="nama_lengkap">
                        Nama Lengkap
                    </label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-merah focus:border-transparent" placeholder="Masukkan nama lengkap" value="{{ old('nama_lengkap') }}" required>
                    @error('nama_lengkap')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="alamat">
                        Alamat
                    </label>
                    <textarea id="alamat" name="alamat" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-merah focus:border-transparent" placeholder="Masukkan alamat lengkap">{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="nomor_telepon">
                        Nomor Telepon
                    </label>
                    <input type="tel" id="nomor_telepon" name="nomor_telepon" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-merah focus:border-transparent" placeholder="Masukkan nomor telepon" value="{{ old('nomor_telepon') }}">
                    @error('nomor_telepon')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                        Kata Sandi
                    </label>
                    <input type="password" id="password" name="password" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-merah focus:border-transparent" placeholder="••••••••" required>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="password_confirmation">
                        Konfirmasi Kata Sandi
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-merah focus:border-transparent" placeholder="••••••••" required>
                    @error('password_confirmation')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full bg-merah text-white py-3 rounded-xl font-bold hover:bg-red-700 transition shadow-lg">
                    Daftar
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-600 text-sm">
                    Sudah punya akun? 
                    <a href="{{ route('login') }}" class="text-merah font-semibold hover:underline">Masuk di sini</a>
                </p>
            </div>
        </div>
        
        <div class="text-center mt-6">
            <a href="{{ url('/') }}" class="text-white hover:text-emas flex items-center justify-center">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</body>
</html>