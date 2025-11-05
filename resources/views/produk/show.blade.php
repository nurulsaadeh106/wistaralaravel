<!-- show.blade.php -->
@extends('layouts.app')

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 2rem 1rem;">
    <div style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
        <!-- Product Details -->
        <div>
            <!-- Product content here -->
        </div>

        <!-- Reviews Section -->
        <div>
            @include('components.product-review', ['productId' => $product->id_produk])
        </div>
    </div>
</div>
@endsection
