<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keranjang extends Model
{
    use HasFactory;

    /**
     * Nama tabel
     */
    protected $table = 'keranjang';

    /**
     * Kolom-kolom yang bisa diisi
     */
    protected $fillable = [
        'id_user',
        'id_produk',
        'jumlah',
    ];

    /**
     * Relasi dengan user
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Relasi dengan produk
     */
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }
}