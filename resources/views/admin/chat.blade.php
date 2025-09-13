<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | PetShop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Boxicons CDN -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin/adminchat.css') }}">
    <link rel="stylesheet" href="{{ asset('css/adminnavbar.css') }}">
</head>
<body>
    <div class="dashboard-wrapper">
       <!-- Back Button -->
  <a href="{{ route('admin.dashboard') }}" class="back-btn">
    <i class='bx bx-arrow-back'></i> Kembali
  </a>
        <!-- Main Content -->
        <main class="main-content">
            <!-- Navbar -->
    @include('partials.adminnavbar')

           
            <div class="chat-wrapper">

                <!-- Sidebar -->
<div class="chat-sidebar">
    <div class="sidebar-header">
        <h2>Pesan Pengguna</h2>
    </div>

    <div class="sidebar-search">
        <i class='bx bx-search'></i>
        <input type="text" placeholder="Cari Pengguna...">
    </div>

    <div class="sidebar-list">
        @php
            // make sure $conversations is a Collection and sort by last_message->created_at desc
            $sortedConversations = collect($conversations)->sortByDesc(function($conv) {
                return $conv->last_message?->created_at ?? null;
            });
        @endphp

        @foreach($sortedConversations as $conv)
            @php
                // use the unread_count property set in your controller
                $unreadCount = $conv->unread_count ?? 0;
            @endphp

            <a href="{{ route('admin.chat.show', $conv->id) }}"
               class="chat-contact {{ optional($activeUser)->id == $conv->id ? 'active' : '' }}">
                <img
                    src="{{ $conv->avatar ? asset('storage/' . $conv->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($conv->name) }}"
                    class="contact-avatar"
                    alt="{{ $conv->name }}">
                <div class="contact-info">
                    <h4>{{ $conv->name }}</h4>
                    <p>{{ $conv->last_message?->message ?? 'Belum ada pesan' }}</p>
                </div>
                <div class="contact-meta">
                    <span class="contact-time">{{ $conv->last_message?->created_at?->format('d M') ?? '' }}</span>
                    @if($unreadCount > 0)
                        <span class="badge unread-badge">{{ $unreadCount }}</span>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
</div>

<!-- Chat Box -->
<div class="chat-box">
    @if($activeUser)
        <div class="chat-header">
            <img
                src="{{ $activeUser->avatar ? asset('storage/' . $activeUser->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($activeUser->name) }}"
                class="contact-avatar"
                alt="{{ $activeUser->name }}">
            <div>
                <h3>{{ $activeUser->name }}</h3>
                <span class="online-status">Pengguna</span>
            </div>
        </div>

        <div class="chat-messages">
            @foreach($messages as $msg)
                <div class="chat-message {{ $msg->sender_id == auth()->id() ? 'sent' : 'received' }}">
                    <div class="message-bubble">{{ $msg->message }}</div>
                </div>
            @endforeach
        </div>

        <form action="{{ route('admin.chat.send', $activeUser->id) }}" method="POST" class="chat-form">
            @csrf
            <input type="text" name="message" placeholder="Tulis pesan...">
            <button type="submit">Kirim</button>
        </form>
    @else
        <div class="chat-empty">
            <i class="bx bx-chat"></i>
            <p>Pilih pengguna untuk mulai mengobrol!</p>
        </div>
    @endif
</div>

            </div>
        </main>
    </div>
    <script>
document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.querySelector(".sidebar-search input");
    const contacts = document.querySelectorAll(".chat-contact");

    searchInput.addEventListener("input", () => {
        const query = searchInput.value.toLowerCase();
        contacts.forEach(contact => {
            const name = contact.querySelector("h4").textContent.toLowerCase();
            contact.style.display = name.includes(query) ? "flex" : "none";
        });
    });
});
</script>

</body>
</html>
