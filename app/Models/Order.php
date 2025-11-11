<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false; // ⚠️ nonaktifkan auto increment
    protected $keyType = 'string';

    protected $fillable = [
        'id',
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

    public static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $today = now()->format('Ymd');
            $random = strtoupper(Str::random(4)); // contoh: AX3P
            $order->id = "WST-{$today}-{$random}";
        });
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
}
