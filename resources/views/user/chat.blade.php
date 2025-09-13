<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Obrolan Pengguna</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Fonts & Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

  <!-- Styles -->
  <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
  <link rel="stylesheet" href="{{ asset('css/chat.css') }}">
</head>
<body>
  {{-- Navbar --}}
  @include('partials.navbar')

  <div class="chat-wrapper">

      <!-- Sidebar -->
      <div class="chat-sidebar">
          <div class="sidebar-header">
              <h2>Pesan</h2>
          </div>

          <div class="sidebar-search">
    <i class='bx bx-search'></i>
    <input type="text" placeholder="Cari admin...">
</div>

          <div class="sidebar-list">
              @forelse($conversations as $conv)
                  <a href="{{ route('user.chat.show', $conv->id) }}"
                     class="chat-contact {{ $activeUser && $activeUser->id == $conv->id ? 'active' : '' }}">
                      <img src="{{ $conv->avatar ? asset('storage/' . $conv->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($conv->name) }}" 
                                class="contact-avatar">
                      <div class="contact-info">
                          <h4>{{ $conv->name }}</h4>
                          <p>{{ $conv->last_message?->message ?? 'Belum ada pesan' }}</p>
                      </div>
                      <div class="contact-meta">
                          <span class="contact-time">
                              {{ $conv->last_message?->created_at->format('d M') }}
                          </span>
                          @if($conv->unread_count > 0)
                              <span class="badge">{{ $conv->unread_count }}</span>
                          @endif
                      </div>
                  </a>
              @empty
                  <p class="no-conversation">Belum ada percakapan</p>
              @endforelse
          </div>
      </div>

      <!-- Chat Box -->
      <div class="chat-box">
          @if($activeUser)
          <div class="chat-header">
              <img src="{{ $conv->avatar ? asset('storage/' . $conv->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($conv->name) }}" 
                                class="contact-avatar">
              <div>
                  <h3>{{ $activeUser->name }}</h3>
                  <span class="online-status">Admin</span>
              </div>
          </div>

          <div class="chat-messages">
              @forelse($messages as $msg)
                  <div class="chat-message {{ $msg->sender_id == auth()->id() ? 'sent' : 'received' }}">
                      <div class="message-bubble">
                          {{ $msg->message }}
                          <span class="message-time">{{ $msg->created_at->format('H:i') }}</span>
                      </div>
                  </div>
              @empty
                  <div class="chat-empty">
                      <i class="bx bx-chat"></i>
                      <p>Chat kosong, kirim pesan pertama kamu!</p>
                  </div>
              @endforelse
          </div>

          <form action="{{ route('user.chat.send', $activeUser->id) }}" method="POST" class="chat-form">
              @csrf
              <input type="text" name="message" placeholder="Tulis pesan..." required>
              <button type="submit">Kirim</button>
          </form>
          @else
          <div class="chat-empty">
              <i class="bx bx-chat"></i>
              <p>Pilih admin untuk mulai mengobrol!</p>
          </div>
          @endif
      </div>
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
