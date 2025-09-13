<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    /**
     * Show all orders in admin dashboard.
     */
    public function index()
    {
        // Fetch all orders with related user and products
        $orders = Order::with(['user', 'orderItems.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('admin.order', compact('orders'));
    }

    /**
     * Show the details of a specific order (for modal popup).
     */
    public function details($id)
    {
        $order = Order::with(['user', 'orderItems.product'])->findOrFail($id);

        // Return partial view for modal content
        return view('admin.order.show', compact('order'));
    }
    public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:pending,diproses,dikirim,selesai,batal'
    ]);

    $order = Order::findOrFail($id);
    $order->status_pesanan = $request->status;
    $order->save();

    return response()->json(['success' => true]);
}


}
