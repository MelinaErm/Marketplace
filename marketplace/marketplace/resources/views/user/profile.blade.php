@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('User Profile') }}</div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            @if ($user->photo)
                            <img src="{{ asset('storage/' . $user->photo) }}" alt="Profile Picture" class="img-fluid mb-3">
                            @else
                            <p>No photo available</p>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <h2>{{ $user->name }}</h2>
                            <p>Email: {{ $user->email }}</p>
                            <p>Interests: {{ $user->interests }}</p>
                            <p>Joined at: {{ $user->created_at->format('M d, Y') }}</p>
                            
                            <!-- Display user's products -->
                            @if ($user->products->isNotEmpty())
                            <h3>Products:</h3>
                            <ul>
                                @foreach ($user->products as $product)
                                <li><a href="{{ route('products.show', $product->id) }}">{{ $product->name }}</a></li>
                                @endforeach
                            </ul>
                            @else
                            <p>No products available</p>
                            @endif
                            <!-- End of displaying user's products -->
                            
                            <!-- Add more user information here as needed -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection