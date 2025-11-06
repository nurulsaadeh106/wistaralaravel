<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class UserReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::where('user_id', Auth::id())
            ->with('produk')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('dashboard.reviews', compact('reviews'));
    }
}
