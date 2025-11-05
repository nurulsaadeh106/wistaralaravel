<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'nama',
        'telepon',
        'alamat',
        'catatan',
        'total',
        'status',
        'tipe_order',
        'metode_pembayaran',
        'tanggal_ambil',
        'tipe_order',
        'metode_pembayaran',
        'bukti_pembayaran',
        'status_pembayaran',
        'tanggal_ambil',
        'status',
    ];

    public function items()
    {
        return $this->hasMany(\App\Models\OrderItem::class, 'order_id', 'id');
    }
}
