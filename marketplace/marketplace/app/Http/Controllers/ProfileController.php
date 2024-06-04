<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\User;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
    $user = $request->user();
    $favoriteProducts = $user->favoriteProducts; 
    return view('profile.show', compact('user', 'favoriteProducts'));
    }


    public function edit()
    {
        $user = auth()->user();
        return View::make('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'photo' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'interests' => 'nullable|string|max:255',
        ]);

        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'interests' => $request->input('interests'),
        ];

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos', 'public');
            $data['photo'] = $photoPath;
        }

        $user->update($data);

        return redirect()->route('profile')->with('success', 'Your profile has been updated successfully.');
    }

    public function destroy()
    {
        $user = auth()->user();
        $user->delete();
        return redirect('/')->with('success', 'Your account has been deleted successfully.');
    }
}
