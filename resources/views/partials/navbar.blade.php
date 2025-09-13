<nav class="navbar">
    <div class="logo">
        <img src="{{ asset('asset/logofull.png') }}" alt="PawMart Logo" style="width:150px; height:auto;">

    </div>
    <ul>
        <li><a href="{{ route('dashboard') }}">Beranda</a></li>
        <li><a href="{{ route('shop') }}">Produk</a></li>
        <li><a href="{{ route('about') }}">Tentang kami</a></li>
    </ul>
    <div class="search-icons">
        <div class="search-bar">
    <form action="{{ route('shop') }}" method="GET">
        <button type="submit" id="search-btn" class="search-icon-btn">
            <i class='bx bx-search'></i>
        </button>
        <input type="hidden" name="kategori" value="{{ $activeCategory ?? '' }}">
<input type="text" name="q" value="{{ $search ?? '' }}" placeholder="Cari produk..." style="padding:8px 12px;">


    </form>
</div>

        <!-- LOGINABLE USER ICON -->
        @php
            use App\Models\Message;
            $unreadCount = auth()->check()
                ? Message::where('receiver_id', auth()->id())
                    ->where('is_read', false)
                    ->count()
                : 0;
        @endphp
        @auth
            <a href="{{ route('user.profile') }}" class="icon-link"><i class='bx bx-user'></i></a>
            <a href="{{ route('user.cart') }}" class="icon-link"><i class='bx bx-cart'></i></a>
            <a href="{{ route('user.chat.index') }}" class="icon-link chat-icon">
        <i class='bx bx-chat'></i>
        @if(isset($unreadCount) && $unreadCount > 0)
            <span class="chat-badge">{{ $unreadCount }}</span>
        @endif
    </a>
        @else
            <a href="{{ route('login') }}" class="btn-login">
                <i class='bx bx-log-in'></i> Login
            </a>
        @endauth
    </div>
</nav>