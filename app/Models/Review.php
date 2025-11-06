<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'id_produk',
        'rating',
        'comment',
        'photos',
        'video'
    ];

    protected $casts = [
        'photos' => 'array',
        'rating' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }
}

