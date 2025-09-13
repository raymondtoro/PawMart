<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Message;

class ChatController extends Controller
{
    /* ========== USER CHAT ========== */
    public function indexUser()
    {
        $admins = User::where('role', 'admin')->get()->map(function ($admin) {
            $admin->last_message = Message::where(function ($q) use ($admin) {
                $q->where('sender_id', auth()->id())
                  ->where('receiver_id', $admin->id);
            })->orWhere(function ($q) use ($admin) {
                $q->where('sender_id', $admin->id)
                  ->where('receiver_id', auth()->id());
            })->latest()->first();

            $admin->unread_count = Message::where('sender_id', $admin->id)
                ->where('receiver_id', auth()->id())
                ->where('is_read', false)
                ->count();

            return $admin;
        });

        // Sort by latest message
        $admins = $admins->sortByDesc(fn($a) => $a->last_message?->created_at ?? now()->subYears(10));

        return view('user.chat', [
            'conversations' => $admins,
            'activeUser' => null,
            'messages' => []
        ]);
    }

    public function showUser($adminId)
    {
        $admin = User::where('role', 'admin')->findOrFail($adminId);

        // Mark all unread messages from this admin as read
        Message::where('sender_id', $admin->id)
            ->where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = Message::where(function ($q) use ($admin) {
                $q->where('sender_id', auth()->id())
                  ->where('receiver_id', $admin->id);
            })
            ->orWhere(function ($q) use ($admin) {
                $q->where('sender_id', $admin->id)
                  ->where('receiver_id', auth()->id());
            })
            ->orderBy('created_at', 'asc')
            ->get();

        $admins = User::where('role', 'admin')->get()->map(function ($admin) {
            $admin->last_message = Message::where(function ($q) use ($admin) {
                $q->where('sender_id', auth()->id())
                  ->where('receiver_id', $admin->id);
            })->orWhere(function ($q) use ($admin) {
                $q->where('sender_id', $admin->id)
                  ->where('receiver_id', auth()->id());
            })->latest()->first();

            $admin->unread_count = Message::where('sender_id', $admin->id)
                ->where('receiver_id', auth()->id())
                ->where('is_read', false)
                ->count();

            return $admin;
        });

        // Sort by latest message
        $admins = $admins->sortByDesc(fn($a) => $a->last_message?->created_at ?? now()->subYears(10));

        return view('user.chat', [
            'conversations' => $admins,
            'activeUser' => $admin,
            'messages' => $messages
        ]);
    }

    public function sendUser(Request $request, $adminId)
    {
        $request->validate(['message' => 'required|string']);

        Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $adminId,
            'message' => $request->message,
            'is_read' => false
        ]);

        return back();
    }

    /* ========== ADMIN CHAT ========== */
    public function indexAdmin()
    {
        $users = User::where('role', 'user')->get()->map(function ($user) {
            $user->last_message = Message::where(function ($q) use ($user) {
                $q->where('sender_id', auth()->id())
                  ->where('receiver_id', $user->id);
            })->orWhere(function ($q) use ($user) {
                $q->where('sender_id', $user->id)
                  ->where('receiver_id', auth()->id());
            })->latest()->first();

            $user->unread_count = Message::where('sender_id', $user->id)
                ->where('receiver_id', auth()->id())
                ->where('is_read', false)
                ->count();

            return $user;
        });

        // Sort by latest message
        $users = $users->sortByDesc(fn($u) => $u->last_message?->created_at ?? now()->subYears(10));

        return view('admin.chat', [
            'conversations' => $users,
            'activeUser' => null,
            'messages' => []
        ]);
    }

    public function showAdmin($userId)
    {
        $user = User::where('role', 'user')->findOrFail($userId);

        // Mark all unread messages from this user as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = Message::where(function ($q) use ($user) {
                $q->where('sender_id', $user->id)
                  ->where('receiver_id', auth()->id());
            })
            ->orWhere(function ($q) use ($user) {
                $q->where('sender_id', auth()->id())
                  ->where('receiver_id', $user->id);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        $users = User::where('role', 'user')->get()->map(function ($user) {
            $user->last_message = Message::where(function ($q) use ($user) {
                $q->where('sender_id', auth()->id())
                  ->where('receiver_id', $user->id);
            })->orWhere(function ($q) use ($user) {
                $q->where('sender_id', $user->id)
                  ->where('receiver_id', auth()->id());
            })->latest()->first();

            $user->unread_count = Message::where('sender_id', $user->id)
                ->where('receiver_id', auth()->id())
                ->where('is_read', false)
                ->count();

            return $user;
        });

        // Sort by latest message
        $users = $users->sortByDesc(fn($u) => $u->last_message?->created_at ?? now()->subYears(10));

        return view('admin.chat', [
            'conversations' => $users,
            'activeUser' => $user,
            'messages' => $messages
        ]);
    }

    public function sendAdmin(Request $request, $userId)
    {
        $request->validate(['message' => 'required|string']);

        Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $userId,
            'message' => $request->message,
            'is_read' => false
        ]);

        return back();
    }
}
