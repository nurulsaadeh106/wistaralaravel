<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'nama',
        'telepon',
        'alamat',
        'catatan',
        'total',
        'status',
        'status_pembayaran',
        'bukti_pembayaran',
        'tipe_order',
        'metode_pembayaran',
        'tanggal_ambil'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
}
