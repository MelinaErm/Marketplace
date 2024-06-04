<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function store(Product $product)
    {
        $user = Auth::user();

        //check if the product is already in the user's favorites
        if ($user->favoriteProducts()->where('product_id', $product->id)->exists()) {
            return back()->with('error', 'You have already saved this product!');
        }

        $user->favoriteProducts()->attach($product->id);

        return back()->with('success', 'Product added to favorites');
    }

    public function delete(Product $product)
    {
        Auth::user()->favoriteProducts()->detach($product->id);

        return back()->with('success', 'Product removed from favorites');
    }
}
