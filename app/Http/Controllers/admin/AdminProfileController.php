<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class AdminProfileController extends Controller
{
    public function profile(Request $request)
    {
        $admin = Auth::user();

        // Handle updates (PUT or POST)
        if ($request->isMethod('put') || $request->isMethod('post')) {
            
            // ✅ Decode JSON body if Content-Type is application/json
            if ($request->isJson()) {
                $data = $request->json()->all();
                $request->merge($data);
            }

            $request->validate([
                'name'   => 'sometimes|required|string|max:255',
                'email'  => 'sometimes|required|email',
                'phone'  => 'nullable|string|max:20',
                'bio'    => 'nullable|string',
                'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            // Update avatar if uploaded
            if ($request->hasFile('avatar')) {
                if ($admin->avatar && Storage::disk('public')->exists($admin->avatar)) {
                    Storage::disk('public')->delete($admin->avatar);
                }
                $path = $request->file('avatar')->store('profile_avatars', 'public');
                $admin->avatar = $path;
            }

            // Update text fields
            if ($request->filled('name')) {
                $admin->name = $request->name;
            }
            if ($request->filled('email')) {
                $admin->email = $request->email;
            }
            if ($request->filled('phone')) {
                $admin->phone = $request->phone;
            }
            if ($request->filled('bio')) {
                $admin->bio = $request->bio;
            }

            $admin->save();

            // ✅ If AJAX request
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Profile updated successfully!',
                    'admin'   => $admin,
                ]);
            }

            // If normal form (avatar upload)
            return back()->with('success', 'Profile updated successfully!');
        }

        return view('admin.adminprofile', compact('admin'));
    }
}
