<!-- resources/views/components/product-review.blade.php -->
<style>
    .review-section {
        background: white;
        padding: 1.5rem;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    }

    .star-rating {
        color: #FCD34D; /* text-yellow-400 */
        font-size: 1.5rem;
    }

    .star-rating.inactive {
        color: #D1D5DB; /* text-gray-300 */
    }

    .review-form textarea {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #D1D5DB;
        border-radius: 0.375rem;
        margin-bottom: 1rem;
    }

    .review-form input[type="file"] {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #D1D5DB;
        border-radius: 0.375rem;
        margin-bottom: 1rem;
    }

    .submit-button {
        background: #2563EB;
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 0.375rem;
        cursor: pointer;
    }

    .submit-button:hover {
        background: #1D4ED8;
    }

    .review-item {
        border-bottom: 1px solid #E5E7EB;
        padding-bottom: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .user-avatar {
        width: 3rem;
        height: 3rem;
        background: #E5E7EB;
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .review-photos {
        display: flex;
        gap: 0.5rem;
        overflow-x: auto;
        padding: 0.5rem 0;
    }

    .review-photo {
        width: 6rem;
        height: 6rem;
        object-fit: cover;
        border-radius: 0.375rem;
    }

    .review-video {
        max-width: 100%;
        border-radius: 0.375rem;
    }
</style>

<div class="review-section">
    <!-- Review Form -->
    @auth
    <div class="review-form">
        <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem;">Tulis Review</h3>
        <form id="reviewForm" method="POST" action="{{ route('reviews.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id_produk" value="{{ $productId }}">

            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem;">Rating</label>
                <div class="star-rating">
                    @for ($i = 1; $i <= 5; $i++)
                        <span class="star" data-rating="{{ $i }}"
                              onclick="setRating({{ $i }})"
                              style="cursor: pointer;">★</span>
                    @endfor
                </div>
                <input type="hidden" name="rating" id="ratingInput" required>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem;">Komentar</label>
                <textarea name="comment" rows="4" required></textarea>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem;">Foto Produk (Opsional)</label>
                <input type="file" name="photos[]" multiple accept="image/*">
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem;">Video Review (Opsional)</label>
                <input type="file" name="video" accept="video/*">
            </div>

            <button type="submit" class="submit-button">Kirim Review</button>
        </form>
    </div>
    @else
    <p style="text-align: center; padding: 1rem;">
        <a href="{{ route('login') }}" style="color: #2563EB;">Login</a> untuk menulis review
    </p>
    @endauth

    <!-- Reviews List -->
    <div style="margin-top: 2rem;">
        <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem;">
            Reviews ({{ count($reviews) }})
        </h3>

        @if(count($reviews) == 0)
            <p style="text-align: center; color: #6B7280;">Belum ada review untuk produk ini.</p>
        @else
            @foreach($reviews as $review)
            <div class="review-item">
                <div style="display: flex; justify-content: space-between;">
                    <div style="display: flex; gap: 1rem;">
                        <div class="user-avatar">
                            <span style="font-size: 1.25rem;">{{ substr($review->user->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <h4 style="font-weight: 600;">{{ $review->user->name }}</h4>
                            <div class="star-rating">
                                @for ($i = 1; $i <= 5; $i++)
                                    <span class="{{ $i <= $review->rating ? '' : 'inactive' }}">★</span>
                                @endfor
                            </div>
                            <p style="margin-top: 0.5rem; color: #4B5563;">{{ $review->comment }}</p>
                        </div>
                    </div>
                    <span style="color: #9CA3AF; font-size: 0.875rem;">
                        {{ $review->created_at->format('d M Y') }}
                    </span>
                </div>

                @if($review->photos)
                <div class="review-photos">
                    @foreach(json_decode($review->photos) as $photo)
                        <img src="{{ Storage::url($photo) }}" class="review-photo"
                             onclick="showImage('{{ Storage::url($photo) }}')">
                    @endforeach
                </div>
                @endif

                @if($review->video)
                <div style="margin-top: 1rem;">
                    <video src="{{ Storage::url($review->video) }}"
                           controls class="review-video"></video>
                </div>
                @endif
            </div>
            @endforeach
        @endif
    </div>
</div>

<script>
function setRating(rating) {
    document.getElementById('ratingInput').value = rating;
    const stars = document.querySelectorAll('.star');
    stars.forEach((star, index) => {
        star.classList.toggle('inactive', index >= rating);
    });
}

function showImage(url) {
    // Buat lightbox sederhana
    const overlay = document.createElement('div');
    overlay.style.position = 'fixed';
    overlay.style.top = '0';
    overlay.style.left = '0';
    overlay.style.width = '100%';
    overlay.style.height = '100%';
    overlay.style.backgroundColor = 'rgba(0,0,0,0.9)';
    overlay.style.display = 'flex';
    overlay.style.alignItems = 'center';
    overlay.style.justifyContent = 'center';
    overlay.style.zIndex = '1000';

    const img = document.createElement('img');
    img.src = url;
    img.style.maxHeight = '90%';
    img.style.maxWidth = '90%';
    img.style.objectFit = 'contain';

    overlay.appendChild(img);
    overlay.onclick = () => overlay.remove();
    document.body.appendChild(overlay);
}

// Flash message handling
@if(session('success'))
    alert("{{ session('success') }}");
@endif

@if($errors->any())
    alert("{{ $errors->first() }}");
@endif
</script>
