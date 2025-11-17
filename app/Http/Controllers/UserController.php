<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    // Get all users
    public function index()
    {
        $users = User::all()->map(function($user) {
            $user->profile_image_url = $user->profile_image ? asset('storage/'.$user->profile_image) : null;
            return $user;
        });
        return response()->json(['success' => true, 'data' => $users]);
    }

    // Create new user
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'profile_image' => 'nullable|image|max:2048'
        ]);

        $path = null;
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_images', 'public');
        }

        $user = User::create([
            'name' => $request->name,
            'subject' => $request->subject,
            'profile_image' => $path
        ]);

        $user->profile_image_url = $path ? asset('storage/'.$path) : null;

        return response()->json(['success' => true, 'data' => $user], 201);
    }

    // Get single user
    public function show($id)
    {
        $user = User::findOrFail($id);
        $user->profile_image_url = $user->profile_image ? asset('storage/'.$user->profile_image) : null;

        return response()->json(['success' => true, 'data' => $user]);
    }

    // Update user
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'profile_image' => 'nullable|image|max:2048'
        ]);

        $user = User::findOrFail($id);

        $path = $user->profile_image;

        if ($request->hasFile('profile_image')) {
            if ($path && Storage::exists("public/$path")) {
                Storage::delete("public/$path");
            }
            $path = $request->file('profile_image')->store('profile_images', 'public');
        }

        $user->update([
            'name' => $request->name,
            'subject' => $request->subject,
            'profile_image' => $path
        ]);

        $user->profile_image_url = $path ? asset('storage/'.$path) : null;

        return response()->json(['success' => true, 'data' => $user]);
    }

    // Delete user
    public function destroy($id)
{
    $user = User::findOrFail($id);

    // Delete image from folder using unlink()
    if ($user->profile_image) {
        $imagePath = public_path('profile_images/' . $user->profile_image);

        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    // Delete user
    $user->delete();

    return response()->json([
        'success' => true,
        'message' => 'User and image deleted successfully'
    ]);
}

}
