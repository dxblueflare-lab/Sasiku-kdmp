<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username_or_email' => 'required',
            'password' => 'required',
        ]);

        // Ubah nama field agar sesuai dengan kolom di database
        $loginType = filter_var($request->username_or_email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials = [
            $loginType => $request->username_or_email,
            'password' => $request->password
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Redirect berdasarkan role pengguna
            $user = Auth::user();
            switch ($user->role) {
                case 'admin':
                    return redirect()->intended('/admin/dashboard');
                case 'supplier':
                    return redirect()->intended('/supplier/dashboard');
                case 'customer':
                    return redirect()->intended('/customer/dashboard');
                default:
                    return redirect()->intended('/');
            }
        }

        return back()->withErrors([
            'login' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
        ])->withInput($request->only('username_or_email'));
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'username' => 'required|unique:users,username|max:255',
            'email' => 'required|unique:users,email|email|max:255',
            'password' => 'required|min:8|confirmed',
            'nama_lengkap' => 'required|max:255',
            'alamat' => 'nullable',
            'nomor_telepon' => 'nullable',
        ]);

        $validatedData['password'] = Hash::make($validatedData['password']);
        // Default role untuk registrasi adalah customer
        $validatedData['role'] = 'customer';

        $user = User::create($validatedData);

        // Login otomatis setelah registrasi
        Auth::login($user);

        return redirect()->intended('/customer/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}