<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'order_items';

    protected $fillable = [
        'order_id',
        'id_produk',
        'qty',
        'harga',
        'subtotal'
    ];

    /**
     * 🔗 Relasi ke tabel Order
     * order_id sekarang berupa string (misal: WST-20251110-AX3P)
     */
    public function order()
    {
        return $this->belongsTo(\App\Models\Order::class, 'order_id', 'id');
    }

    /**
     * 🔗 Relasi ke tabel Produk
     */
    public function produk()
    {
        return $this->belongsTo(\App\Models\Produk::class, 'id_produk', 'id_produk');
    }
}
