<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\View;

class ProductController extends Controller
{
    public function store(Request $request)
    {
    try {
        //validate the request
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric|min:0',
            'category' => 'required',
            'shipping_method' => 'required',
            'map' => 'nullable',
            'photo_product' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'availability' => 'nullable|boolean',
        ]);

        //set a default value for "availability" if not provided
        $availability = $request->input('availability', 1); 

        //handle file upload
        $filename = null;
        if ($request->hasFile('photo_product')) {
            $image = $request->file('photo_product');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('storage'), $filename);
        }

        //create the product
        $product = new Product();
        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->price = $request->input('price');
        $product->category = $request->input('category');
        $product->shipping_method = $request->input('shipping_method');
        $product->map = $request->input('map');
        $product->user_id = auth()->id();
        $product->photo_product = $filename;
        $product->availability = $availability; 
        $product->save();

        return redirect()->back()->with('status', 'Product added successfully!');
    } catch (\Exception $e) {

        return redirect()->back()->with('error', 'Failed to add product: ' . $e->getMessage());
    }
}



    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function index(Request $request)
    {
        //retrieve products posted by the currently logged-in user
        $userProducts = auth()->user()->products;

        //retrieve products from other users
        $query = Product::with('user')->whereNotIn('user_id', [auth()->id()]);

        //apply search filter
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            });
        }

        //apply sort filter
        if ($request->has('sort')) {
            $sort = $request->input('sort');
            if ($sort == 'newest') {
                $query->orderBy('created_at', 'desc');
            } elseif ($sort == 'oldest') {
                $query->orderBy('created_at', 'asc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $otherProducts = $query->get();

        return view('home', compact('userProducts', 'otherProducts'));
    }

    public function edit(Product $product)
    {
        if ($product->user_id !== auth()->id()) {
            return redirect()->route('home')->with('error', 'Unauthorized access.');
        }

        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
{
    if ($product->user_id !== auth()->id()) {
        return redirect()->route('home')->with('error', 'Unauthorized access.');
    }

    $request->validate([
        'name' => 'required',
        'description' => 'required',
        'price' => 'required|numeric|min:0',
        'category' => 'required',
        'shipping_method' => 'required',
        'map' => 'required',
        'photo_product' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        'availability' => 'required|boolean',
    ]);

    //handle file upload
    if ($request->hasFile('photo_product')) {
        $image = $request->file('photo_product');
        $filename = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('storage'), $filename);
        $product->photo_product = $filename;
    }

    //remove photo if checkbox is checked
    if ($request->has('remove_photo')) {
        if ($product->photo_product) {
            $path = public_path('storage/' . $product->photo_product);
            if (file_exists($path)) {
                unlink($path);
            }
            $product->photo_product = null;
        }
    }

    $product->name = $request->input('name');
    $product->description = $request->input('description');
    $product->price = $request->input('price');
    $product->category = $request->input('category');
    $product->shipping_method = $request->input('shipping_method');
    $product->map = $request->input('map');
    $product->availability = $request->input('availability');
    $product->save();

    return redirect()->route('home')->with('status', 'Product updated successfully!');
}

    public function destroy(Product $product)
    {
        if ($product->user_id !== auth()->id()) {
            return redirect()->route('home')->with('error', 'Unauthorized access.');
        }

        if ($product->photo_product) {
            $path = public_path('storage/' . $product->photo_product);
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $product->delete();
        return redirect()->back()->with('success', 'Product deleted successfully.');
    }
}
