<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    public function index()
    {
        return response()->json(Review::with('user', 'product')->get());
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'id_produk' => 'required|exists:produk,id_produk',
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'required|string',
        'photos.*' => 'nullable|image|max:2048',
        'video' => 'nullable|mimes:mp4,mov,avi|max:10240',
    ]);

    $review = new Review($validated);
    $review->user_id = $request->user()->id; // Ambil otomatis dari token login

    // Simpan foto
    $photos = [];
    if ($request->hasFile('photos')) {
        foreach ($request->file('photos') as $photo) {
            $photos[] = $photo->store('reviews/photos', 'public');
        }
        $review->photos = json_encode($photos);
    }

    // Simpan video
    if ($request->hasFile('video')) {
        $review->video = $request->file('video')->store('reviews/videos', 'public');
    }

    $review->save();

    return response()->json([
        'message' => 'Review berhasil ditambahkan!',
        'data' => $review
    ], 201);
}

    public function show($id)
    {
        return response()->json(Review::with('user', 'product')->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        $validated = $request->validate([
            'rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review->update($validated);
        return response()->json(['message' => 'Review diperbarui!', 'data' => $review]);
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return response()->json(['message' => 'Review dihapus!']);
    }
}
