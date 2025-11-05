@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-semibold mb-6">Review Saya</h2>

        <!-- Daftar Review -->
        <div class="space-y-6">
            @forelse($reviews as $review)
            <div class="border rounded-lg p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="font-semibold">{{ $review->produk->nama }}</h3>
                        <div class="flex items-center mt-1">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $review->rating)
                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endif
                            @endfor
                        </div>
                        <p class="mt-2 text-gray-600">{{ $review->komentar }}</p>

                        <!-- Gambar Review -->
                        @if($review->foto)
                        <div class="mt-3">
                            <img src="{{ asset('storage/' . $review->foto) }}" alt="Review photo" class="w-32 h-32 object-cover rounded">
                        </div>
                        @endif

                        <!-- Video Review -->
                        @if($review->video)
                        <div class="mt-3">
                            <video controls class="w-64 rounded">
                                <source src="{{ asset('storage/' . $review->video) }}" type="video/mp4">
                                Browser Anda tidak mendukung tag video.
                            </video>
                        </div>
                        @endif

                        <div class="mt-2 text-sm text-gray-500">
                            {{ $review->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center text-gray-500 py-8">
                <p>Anda belum membuat review apapun.</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($reviews->hasPages())
        <div class="mt-6">
            {{ $reviews->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
