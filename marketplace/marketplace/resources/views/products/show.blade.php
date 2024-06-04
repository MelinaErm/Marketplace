@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ $product->name }}</div>

                <!-- Add to Favorites Button -->
                @php
                    $user = Auth::user();
                    $isFavorite = false;

                    if ($user) {
                        $isFavorite = $user->favoriteProducts()->where('product_id', $product->id)->exists();
                    }
                @endphp

                @if(auth()->check() && auth()->user()->id == $product->user_id)
                    <p class="btn btn-warning favorite-btn">Your Product</p>
                @else
                    @if ($isFavorite)
                        <form action="{{ route('favorites.delete', $product->id) }}" method="POST" style="display: inline;" class="favorite-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-warning favorite-btn">⭐ Remove from Favorites</button>
                        </form>
                    @else
                        <form action="{{ route('favorites.store', $product->id) }}" method="POST" style="display: inline;" class="favorite-form">
                            @csrf
                            <button type="submit" class="btn btn-warning favorite-btn">⭐ Add to Favorites</button>
                        </form>
                    @endif
                @endif


                <div class="card-body">
                    <div class="text-center"> 
                        <p>{{ $product->description }}</p>
                        <p><strong>Price:</strong> €{{ $product->price }}</p> 
                        <p><strong>Category:</strong> {{ $product->category }}</p> 
                        <p><strong>Shipping Method:</strong> {{ $product->shipping_method }}</p> 
                        <p><strong>Location:</strong> {{ $product->map }}</p> 
                        <p><strong>Availability:</strong> @if ($product->availability)
                            <span class="text-success">Available</span>
                            @else
                            <span class="text-danger">Not Available</span>
                            @endif
                        </p> 
                    </div>
                    <div class="form-group text-center">
                        <label for="photo_product"><strong>Photo of the product:</strong></label>
                        <br>
                        @if ($product->photo_product)
                            <img src="{{ asset('storage/' . $product->photo_product) }}" alt="Product Photo" style="max-width: 200px; max-height: 200px;">
                        @else
                            <p>No photo available</p>
                        @endif
                    </div>
                    <div class="d-flex align-items-center justify-content-end">
                        @if ($product->user->photo)
                            <img src="{{ asset('storage/' . $product->user->photo) }}" alt="Profile Picture" width="50" style="width: 50px; height: 50px; border-radius: 50%;">
                        @else
                            <p>No photo available</p>
                        @endif
                    </div>
                    <div class="d-flex justify-content-end mt-2"> 
                        <p class="text-muted">Posted by: <a href="{{ route('user.profile', $product->user->id) }}">{{ $product->user->name }}</a></p>
                    </div>

                    
                    <div class="mt-3 text-center">
                        @if(auth()->id() == $product->user_id)
                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary">Edit</a>
                        @else
                            @if($product->availability)
                                <a href="{{ route('messages', ['id' => $product->user->id, 'message' => urlencode("Hello, I'm interested in the product '{$product->name}'. Please send me more info.")]) }}" class="btn btn-primary">Message User</a>
                            @endif
                        @endif
                        <a href="{{ route('home') }}" class="btn btn-secondary">Back to Dashboard</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .favorite-btn {
                    position: absolute;
                    top: 10px;
                    right: 10px;
                    font-size: 14px; 
                    padding: 5px 10px; 
                    z-index: 10; 
                        }
</style>


@endsection
