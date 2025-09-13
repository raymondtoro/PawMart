<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class NotificationController extends Controller
{
    // Fetch notifications for admin navbar
    public function fetch()
    {
        $oneHourAgo = now()->subHour();

        $orders = Order::with(['orderItems.produk', 'user'])
    ->where('tanggal_pesanan', '>=', now()->subHours(12)) // widen window for safety
    ->orderBy('tanggal_pesanan', 'desc')
    ->get()
    ->map(function ($order) {
        $firstProduct = $order->orderItems->first()->produk ?? null;

        return [
            'user'       => $order->user->name ?? $order->nama ?? 'Unknown User',
            'produk'     => $firstProduct->nama_produk ?? 'Unknown Product',
            'created_at' => \Carbon\Carbon::parse($order->tanggal_pesanan)
                               ->timezone('Asia/Jakarta')
                               ->toIso8601String(),
        ];
    });

        // Unread messages
        $unreadMessages = Message::where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return response()->json([
            'notifications'  => $orders,
            'unreadMessages' => $unreadMessages,
        ]);
    }


}
