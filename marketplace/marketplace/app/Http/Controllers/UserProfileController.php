<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function show(Request $request, $id)
    {
        //retrieve the user by ID with the products data eager loaded
        $user = User::with('products')->findOrFail($id);

        //check if the request expects JSON
        if ($request->expectsJson()) {
            //transform the photo URL if available
            $user->photo = $user->photo ? url('storage/' . $user->photo) : null;
            return response()->json($user);
        }

        //otherwise, return the Blade view with the user's data
        return view('user.profile', ['user' => $user]);
    }
}

