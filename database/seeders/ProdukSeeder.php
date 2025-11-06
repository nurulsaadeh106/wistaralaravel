<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produk;
use App\Models\KategoriProduk;
use Illuminate\Support\Str;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        // Make sure we have at least one category
        $kategori = KategoriProduk::first() ?? KategoriProduk::create([
            'nama_kategori' => 'Batik',
            'slug' => 'batik'
        ]);

        $products = [
            [
                'nama_produk' => 'Batik Parang Klasik',
                'deskripsi' => 'Batik Parang klasik dengan motif tradisional yang elegan',
                'harga' => 250000.00,
                'stok' => 50,
                'id_kategori' => $kategori->id_kategori,
                'status' => 'aktif',
                'gambar' => 'produk/batik-parang.jpg',
                'link_shopee' => 'https://shopee.co.id/batik-parang',
                'link_tiktok' => 'https://tiktok.com/@wistara/batik-parang'
            ],
            [
                'nama_produk' => 'Batik Mega Mendung',
                'deskripsi' => 'Batik Mega Mendung dengan warna cerah dan motif awan yang menawan',
                'harga' => 300000.00,
                'stok' => 30,
                'id_kategori' => $kategori->id_kategori,
                'status' => 'aktif',
                'gambar' => 'produk/batik-mega-mendung.jpg',
                'link_shopee' => 'https://shopee.co.id/batik-mega-mendung',
                'link_tiktok' => 'https://tiktok.com/@wistara/batik-mega-mendung'
            ],
            [
                'nama_produk' => 'Batik Sekar Jagad',
                'deskripsi' => 'Batik Sekar Jagad dengan paduan motif yang harmonis',
                'harga' => 275000.00,
                'stok' => 40,
                'id_kategori' => $kategori->id_kategori,
                'status' => 'aktif',
                'gambar' => 'produk/batik-sekar-jagad.jpg',
                'link_shopee' => 'https://shopee.co.id/batik-sekar-jagad',
                'link_tiktok' => 'https://tiktok.com/@wistara/batik-sekar-jagad'
            ]
        ];

        foreach ($products as $product) {
            Produk::firstOrCreate(
                ['slug' => Str::slug($product['nama_produk'])],
                array_merge($product, [
                    'slug' => Str::slug($product['nama_produk'])
                ])
            );
        }
    }
}
