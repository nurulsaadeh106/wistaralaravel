<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\User;
use App\Models\Produk;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan ada minimal satu user
        $user = User::first() ?? User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now()
        ]);

        // Ambil semua produk
        $products = Produk::all();

        foreach ($products as $product) {
            // Buat 3-5 review untuk setiap produk
            $numberOfReviews = rand(3, 5);

            for ($i = 0; $i < $numberOfReviews; $i++) {
                Review::create([
                    'user_id' => $user->id,
                    'id_produk' => $product->id_produk,
                    'rating' => rand(4, 5),
                    'komentar' => $this->getRandomComment(),
                    'foto' => null,
                    'video' => null
                ]);
            }
        }
    }

    private function getRandomComment(): string
    {
        $comments = [
            'Kain batiknya sangat berkualitas, motifnya indah dan detail.',
            'Jahitannya rapi dan bahan nyaman dipakai.',
            'Pengiriman cepat dan produk sesuai dengan foto.',
            'Warna dan motif batik sangat menarik, tidak mengecewakan.',
            'Pelayanan ramah dan responsif, recommended seller.',
            'Kualitas produk sangat baik, akan berbelanja lagi.',
            'Desain batiknya unik dan elegan.',
            'Harga sebanding dengan kualitas yang didapat.',
            'Produk original dengan kualitas premium.',
            'Cocok untuk acara formal maupun casual.'
        ];

        return $comments[array_rand($comments)];
    }
}
