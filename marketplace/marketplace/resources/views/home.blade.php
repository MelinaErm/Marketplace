@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @auth
                        <div class="alert alert-info" role="alert">
                            Hello {{ Auth::user()->name }}! You are logged in.
                        </div>
                        @if(isset($unreadMessagesCount) && $unreadMessagesCount > 0)
                            <div class="alert alert-info" role="alert">
                                You have {{ $unreadMessagesCount }} new messages.
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info" role="alert">
                            {{ __('You are logged in!') }}
                        </div>
                    @endauth

                     <!-- Display Success or Error Messages -->
                     @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @elseif (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <button class="btn btn-primary mt-3" id="toggleForm">Add Product</button>

                    <form method="POST" action="{{ route('products.store') }}" id="productForm" style="display: none;" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="price">Price</label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="photo_product">Photo (not larger than 2 MB, acceptable file types: JPEG, PNG, JPG, or GIF.)</label>
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
                            <input type="text" class="form-control" id="map" name="map">
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>

                    <!-- My Products -->
                    <div class="mt-5">
                        <h5>My Products:</h5>
                        @if($userProducts->isEmpty())
                            <p class="text-center">You haven't posted any product.</p>
                        @else
                            <div id="myProductCarousel" class="carousel slide" data-ride="carousel">
                                <div class="carousel-inner">
                                    @foreach($userProducts as $index => $product)
                                        <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                            <div class="card mt-3">
                                                <div class="card-body d-flex flex-column align-items-center">
                                                    <h5 class="card-title">{{ $product->name }}</h5>
                                                    @if ($product->photo_product)
                                                        <img src="{{ asset('storage/' . $product->photo_product) }}" alt="Product Photo" class="img-fluid" style="width: 200px; height: 200px;">
                                                    @else
                                                        <p class="text-center">No photo available</p>
                                                    @endif
                                                    <div class="mt-3">
                                                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-primary">View Details</a>
                                                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-secondary">Edit</a>
                                                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger delete-button">Delete</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <a class="carousel-control-prev" href="#myProductCarousel" role="button" data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#myProductCarousel" role="button" data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Products from Other Users -->

                    <div class="mt-5">
                        <h5>Products from Other Users:</h5>
                        <div class="input-group mt-3">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search..." onkeyup="searchProducts()">
                        </div>
                        <div class="mt-3">
                            <label for="sortFilter">Sort by:</label>
                            <select class="form-control" id="sortFilter" onchange="sortProducts()">
                                <option value="newest">Newest</option>
                                <option value="oldest">Oldest</option>
                            </select>
                        </div>
                        <div id="otherProductList">
                            @foreach($otherProducts as $product)
                                <div class="card mt-3" data-created-at="{{ $product->created_at }}">
                                    <div class="card-body">
                                        <div style="text-align: center;">
                                            <h5 class="card-title">{{ $product->name }}</h5>
                                            @if ($product->photo_product)
                                                <img src="{{ asset('storage/' . $product->photo_product) }}" alt="Product Photo" class="img-fluid" style="width: 200px; height: 200px;">
                                            @else
                                                <p>No photo available</p>
                                            @endif
                                            <p class="card-text"><strong>Price: €{{ $product->price }}</strong></p>
                                            <div class="d-flex justify-content-center align-items-center">
                                                @if ($product->availability)
                                                    <div class="d-flex align-items-center">
                                                        <span class="text-success mr-2">Available</span>
                                                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-primary">View Details</a>
                                                    </div>
                                                @else
                                                    <button class="btn btn-secondary" disabled>Not Available</button>
                                                @endif
                                            </div>

                                        </div>
                                        <div class="posted-by">
                                            <div class="profile-info text-right" style="margin-right: 10px;">
                                                @if ($product->user->photo)
                                                    <img src="{{ asset('storage/' . $product->user->photo) }}" alt="Profile Picture" width="50" style="width: 50px; height: 50px; border-radius: 50%;">
                                                @else
                                                    <p>No photo available</p>
                                                @endif
                                                <p class="card-text">Posted by: <a href="{{ route('user.profile', $product->user->id) }}">{{ $product->user->name }}</a></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <button id="backToTopBtn" class="btn btn-secondary" style="position: fixed; bottom: 20px; right: 20px; display: none;">Back to Top</button>

                    <style>
                        .posted-by {
                            position: absolute;
                            bottom: 5px;
                            right: 5px;
                        }

                        .text-success {
                            color: green;
                            position: absolute;
                            bottom: 5%; 
                            left: 10%; 
                            transform: translateX(-50%); 
                        }

                        .mr-2 {
                            margin-right: 0.5rem;
                        }

                    </style>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

    var toggleFormButton = document.getElementById('toggleForm');
    var productForm = document.getElementById('productForm');

    toggleFormButton.addEventListener('click', function() {
        if (productForm.style.display === 'none') {
            productForm.style.display = 'block';
        } else {
            productForm.style.display = 'none';
        }
    });

    //confirmation before delete
    var deleteButtons = document.querySelectorAll('.delete-button');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(event) {
            var confirmed = confirm('Are you sure you want to delete this product?');
            if (!confirmed) {
                event.preventDefault();
            }
        });
    });

    var backToTopBtn = document.getElementById('backToTopBtn');

    //show the button when the user scrolls down 100px from the top of the document
    window.onscroll = function() {
        if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
            backToTopBtn.style.display = "block";
        } else {
            backToTopBtn.style.display = "none";
        }
    };

    //when the user clicks on the button, scroll to the top of the document
    backToTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

});

function searchProducts() {
    var input, filter, otherProductList, allOtherProducts, i;
    input = document.getElementById('searchInput');
    filter = input.value.toUpperCase();
    otherProductList = document.getElementById('otherProductList');

    allOtherProducts = otherProductList.querySelectorAll('.card');

    for (i = 0; i < allOtherProducts.length; i++) {
        var card = allOtherProducts[i];
        var titleElement = card.querySelector('.card-title');
        var descriptionElement = card.querySelector('.card-text');
        var title = titleElement ? titleElement.innerText.toUpperCase() : '';
        var description = descriptionElement ? descriptionElement.innerText.toUpperCase() : '';
        if (title.indexOf(filter) > -1 || description.indexOf(filter) > -1) {
            card.style.display = "";
        } else {
            card.style.display = "none";
        }
    }
}

function sortProducts() {
    var sortFilter, otherProductList, cards, i, switching, shouldSwitch;
    sortFilter = document.getElementById("sortFilter").value;
    otherProductList = document.getElementById("otherProductList");
    cards = otherProductList.getElementsByClassName("card");
    switching = true;

    while (switching) {
        switching = false;
        for (i = 0; i < (cards.length - 1); i++) {
            shouldSwitch = false;
            if (sortFilter === "newest") {
                if (new Date(cards[i].getAttribute('data-created-at')) < new Date(cards[i + 1].getAttribute('data-created-at'))) {
                    shouldSwitch = true;
                    break;
                }
            } else if (sortFilter === "oldest") {
                if (new Date(cards[i].getAttribute('data-created-at')) > new Date(cards[i + 1].getAttribute('data-created-at'))) {
                    shouldSwitch = true;
                    break;
                }
            }
        }
        if (shouldSwitch) {
            cards[i].parentNode.insertBefore(cards[i + 1], cards[i]);
            switching = true;
        }
    }
}
</script>

@endsection