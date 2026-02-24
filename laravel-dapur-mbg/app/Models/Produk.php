<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produk extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nama tabel
     */
    protected $table = 'produk';

    /**
     * Kolom-kolom yang bisa diisi
     */
    protected $fillable = [
        'nama_produk',
        'deskripsi',
        'harga',
        'stok',
        'id_kategori',
        'id_supplier',
        'gambar_produk',
    ];

    /**
     * Kolom-kolom yang akan di-cast
     */
    protected $casts = [
        'harga' => 'decimal:2',
    ];

    /**
     * Relasi dengan kategori produk
     */
    public function kategori()
    {
        return $this->belongsTo(KategoriProduk::class, 'id_kategori');
    }

    /**
     * Relasi dengan supplier (user)
     */
    public function supplier()
    {
        return $this->belongsTo(User::class, 'id_supplier');
    }

    /**
     * Relasi dengan detail pesanan
     */
    public function detailPesanan()
    {
        return $this->hasMany(DetailPesanan::class, 'id_produk');
    }

    /**
     * Relasi dengan keranjang
     */
    public function keranjang()
    {
        return $this->hasMany(Keranjang::class, 'id_produk');
    }
}