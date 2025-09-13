<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Show the user's profile, orders, and ratings.
     */
    public function index()
{
    $user = Auth::user();

    // Orders that are still in progress
    $ordersProcessing = $user->orders()
        ->whereIn('status_pesanan', ['pending', 'diproses', 'dikirim'])
        ->with(['orderItems.product', 'transaction'])
        ->orderBy('tanggal_pesanan', 'desc')
        ->get()
        ->map(function($order) {
            $subtotal = $order->orderItems->sum(function($item) {
                return $item->harga_saat_beli * $item->jumlah;
            });
            $order->total_with_shipping = $subtotal + ($order->transaction->ongkir ?? 0);
            return $order;
        });

    // Completed orders
    $ordersCompleted = $user->orders()
        ->where('status_pesanan', 'selesai')
        ->with(['orderItems.product', 'transaction'])
        ->orderBy('tanggal_pesanan', 'desc')
        ->get()
        ->map(function($order) {
            $subtotal = $order->orderItems->sum(function($item) {
                return $item->harga_saat_beli * $item->jumlah;
            });
            $order->total_with_shipping = $subtotal + ($order->transaction->ongkir ?? 0);
            return $order;
        });

    // Ratings
    $ratings = $user->ratings()
        ->with('product')
        ->orderBy('created_at', 'desc')
        ->get();

    return view('user.profile', compact(
        'user',
        'ordersProcessing',
        'ordersCompleted',
        'ratings'
    ));
}


    /**
     * Update the user's profile info (name, email, phone).
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
    'name'   => 'sometimes|string|max:255',
    'email'  => 'sometimes|email|max:255|unique:users,email,' . $user->id,
    'phone'  => 'nullable|string|max:20',
    'alamat' => 'nullable|string|max:1000',
]);


        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui!',
            'user'    => $user,
        ]);
    }

    /**
     * Update the user's avatar/profile picture.
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Store new avatar
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
            $user->save();
        }

        return redirect()
            ->route('user.profile')
            ->with('success', 'Foto profil berhasil diperbarui!');
    }
}
