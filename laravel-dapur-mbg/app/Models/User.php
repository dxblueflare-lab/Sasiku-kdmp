<?php

namespace App\Models;

// Import kelas yang diperlukan
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'nama_lengkap',
        'alamat',
        'nomor_telepon',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Relasi dengan produk (seorang supplier dapat memiliki banyak produk)
     */
    public function produk()
    {
        return $this->hasMany(Produk::class, 'id_supplier');
    }

    /**
     * Relasi dengan pesanan (seorang user dapat memiliki banyak pesanan)
     */
    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'id_user');
    }

    /**
     * Relasi dengan keranjang (seorang user dapat memiliki banyak item di keranjang)
     */
    public function keranjang()
    {
        return $this->hasMany(Keranjang::class, 'id_user');
    }
}