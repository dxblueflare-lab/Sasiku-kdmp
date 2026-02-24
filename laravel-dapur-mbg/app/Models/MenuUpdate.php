<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuUpdate extends Model
{
    use HasFactory;

    protected $table = 'menu_updates';

    protected $fillable = [
        'id_supplier',
        'judul_update',
        'deskripsi_update',
        'produk_terupdate',
        'status',
        'tanggal_update'
    ];

    protected $casts = [
        'produk_terupdate' => 'array',
        'tanggal_update' => 'datetime',
    ];

    public function supplier()
    {
        return $this->belongsTo(User::class, 'id_supplier');
    }
}