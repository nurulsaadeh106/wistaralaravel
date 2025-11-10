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

    public function produk()
    {
        return $this->belongsTo(\App\Models\Produk::class, 'id_produk');
    }
}
