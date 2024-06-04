@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Edit Product') }}</div>

                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('products.update', $product->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $product->name }}" required>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required>{{ $product->description }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="price">Price</label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01" value="{{ $product->price }}" required>
                        </div>

                        <div class="form-group">
                            <label for="photo_product">Photo</label>
                            @if ($product->photo_product)
                                <img src="{{ asset('storage/' . $product->photo_product) }}" alt="Current Product Photo" style="max-width: 200px; max-height: 200px;">
                                <br>
                                <input type="checkbox" name="remove_photo" id="remove_photo"> <label for="remove_photo">Remove Photo</label>
                                <br>
                            @endif
                            <input type="file" class="form-control" id="photo_product" name="photo_product">
                        </div>

                        <div class="form-group">
                            <label for="category">Category</label>
                            <select class="form-control" id="category" name="category">
                                <option value="Electronics">Electronics</option>
                                <option value="Clothing and Accessories">Clothing and Accessories</option>
                                <option value="Home and Garden">Home and Garden</option>
                                <option value="Sports">Sports</option>
                                <option value="Books and Media">Books and Media</option>
                                <option value="Health and Beauty">Health and Beauty</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="shipping_method">Shipping Method</label>
                            <select class="form-control" id="shipping_method" name="shipping_method">
                                <option value="Courier">Courier Delivery</option>
                                <option value="User Pickup">User Pickup</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="map">Location</label>
                            <input type="text" class="form-control" id="map" name="map" value="{{ $product->map }}" required>
                        </div>

                        <div class="form-group">
                            <label for="availability">Availability</label>
                            <select class="form-control" id="availability" name="availability">
                                <option value="1" {{ $product->availability ? 'selected' : '' }}>Available</option>
                                <option value="0" {{ !$product->availability ? 'selected' : '' }}>Not Available</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Product</button>
                    </form>

                    <form method="POST" action="{{ route('products.destroy', $product->id) }}">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-danger mt-3" onclick="return confirm('Are you sure you want to delete this product?')">Delete Product</button>
                    </form>

                    <a href="{{ route('home') }}" class="btn btn-secondary mt-3">Back to Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
