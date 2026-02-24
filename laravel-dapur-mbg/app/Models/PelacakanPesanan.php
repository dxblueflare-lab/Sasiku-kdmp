<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PelacakanPesanan extends Model
{
    use HasFactory;

    /**
     * Nama tabel
     */
    protected $table = 'pelacakan_pesanan';

    /**
     * Kolom-kolom yang bisa diisi
     */
    protected $fillable = [
        'id_pesanan',
        'status_pesanan',
        'catatan',
    ];

    /**
     * Relasi dengan pesanan
     */
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan');
    }
}