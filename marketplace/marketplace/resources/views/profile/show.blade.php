@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="profile-box" style="background-color: #ffffff; border-radius: 5px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); padding: 20px; margin-bottom: 20px; text-align: center;">
            <!-- Profile Header -->
            <div class="profile-header">
                <h1>My Profile:</h1>
                <div class="profile-avatar">
                    @if ($user->photo)
                        <img src="{{ asset('storage/' . $user->photo) }}" alt="User Photo" class="profile-photo">
                    @else
                        <p>No photo available</p>
                    @endif
                </div>
            </div>
            <!-- Profile Details -->
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="profile-details">
                        <p><strong>Name:</strong> {{ $user->name }}</p>
                        <p><strong>Email:</strong> {{ $user->email }}</p>
                        <p><strong>Interests:</strong> {{ $user->interests }}</p>
                        <p><strong>Joined:</strong> {{ $user->created_at->format('F j, Y') }}</p>
                    </div>
                </div>
            </div>
            <!-- Profile Actions -->
            <div class="profile-actions">
                <a href="{{ route('edit') }}" class="btn btn-success mr-2">Edit Information</a>
                <form action="{{ route('delete') }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete your account?')">Delete Account</button>
                </form>
                <a href="{{ route('home') }}" class="btn btn-primary ml-2">Go Back</a>
            </div>
        </div>

        <!-- Favourite Products -->
        <div class="favourite-products" style="background-color: #ffffff; border-radius: 5px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); padding: 20px; margin-top: 20px;">
            <h2 style="text-align: center;">My Favourite Products</h2>
            @if($favoriteProducts->isEmpty())
                <p class="text-center">You have no favourite products.</p>
            @else
                <div class="row">
                    @foreach($favoriteProducts as $product)
                        <div class="col-md-4">
                            <div class="card mt-3">
                                <div class="card-body">
                                    <h5 class="card-title" style="text-align: center;">{{ $product->name }}</h5>
                                    @if ($product->photo_product)
                                        <img src="{{ asset('storage/' . $product->photo_product) }}" alt="Product Photo" class="img-fluid" style="width: 100%; height: 200px;">
                                    @else
                                        <p>No photo available</p>
                                    @endif
                                    <p class="card-text" style="text-align: center;"><strong>Price: €{{ $product->price }}</strong></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-primary">View Details</a>
                                        <form action="{{ route('favorites.delete', $product->id) }}" method="POST" class="d-inline ml-2">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to remove this product from favorites?')">Remove from Favorites</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <style>
        .profile-photo {
            border: 5px solid #1877f2; 
            border-radius: 50%; 
            max-width: 200px;
            max-height: 200px;
        }
        .card {
            margin-bottom: 20px;
        }
        .card-body .btn {
            margin: 5px 0; 
        }
    </style>
@endsection
