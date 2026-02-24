<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriProduk extends Model
{
    use HasFactory;

    /**
     * Nama tabel
     */
    protected $table = 'kategori_produk';

    /**
     * Kolom-kolom yang bisa diisi
     */
    protected $fillable = [
        'nama_kategori',
        'deskripsi',
    ];

    /**
     * Relasi dengan produk
     */
    public function produk()
    {
        return $this->hasMany(Produk::class, 'id_kategori');
    }
}