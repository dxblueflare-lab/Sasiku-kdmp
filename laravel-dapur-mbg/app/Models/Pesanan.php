<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    /**
     * Nama tabel
     */
    protected $table = 'pesanan';

    /**
     * Kolom-kolom yang bisa diisi
     */
    protected $fillable = [
        'id_user',
        'total_harga',
        'status_pesanan',
        'metode_pembayaran',
        'bukti_pembayaran',
        'tanggal_pengiriman',
        'alamat_pengiriman',
        'catatan',
    ];

    /**
     * Relasi dengan user
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Relasi dengan detail pesanan
     */
    public function detailPesanan()
    {
        return $this->hasMany(DetailPesanan::class, 'id_pesanan');
    }

    /**
     * Relasi dengan pembayaran
     */
    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'id_pesanan');
    }

    /**
     * Relasi dengan pelacakan pesanan
     */
    public function pelacakanPesanan()
    {
        return $this->hasMany(PelacakanPesanan::class, 'id_pesanan');
    }
}