<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>PawMart - Profil</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('css/user/profile.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

  @include('partials.navbar')

  <div class="container">
    <div class="profile-card">

      <!-- Avatar -->
      <div class="profile-header">
        <div class="avatar">
          <form action="{{ route('user.profile.update-avatar') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <label for="avatar-upload">
              <img id="avatar-preview" src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('asset/default-avatar.png') }}" alt="Avatar">
              <div class="overlay"><i class='bx bx-camera'></i></div>
            </label>
            <input type="file" name="avatar" id="avatar-upload" accept="image/*" hidden>
          </form>
        </div>
        <div>
          <h2 style="margin:0;">{{ $user->name }}</h2>
          <p style="margin:4px 0; color:#6b7280;">Selamat datang di Profilmu!</p>
          <form id="logoutForm" action="{{ route('logout') }}" method="POST">
  @csrf
  <button type="button" id="logoutBtn" 
          style="background:#ef4444;color:#fff;border:none;padding:8px 16px;border-radius:6px;cursor:pointer;">
    Logout
  </button>
</form>

        </div>
      </div>

      <!-- Info -->
<div class="info-card">
  <h3>Info Pribadi</h3>
  <button class="edit-btn" id="editBtn">Edit</button>
  <div class="info-grid" id="infoGrid">
    <div class="info-item">
      <span>Nama</span>
      <div id="nameField" contenteditable="false">{{ $user->name }}</div>
    </div>
    <div class="info-item">
      <span>Email</span>
      <div id="emailField" contenteditable="false">{{ $user->email }}</div>
    </div>
    <div class="info-item">
      <span>No. Handphone</span>
      <div id="phoneField" contenteditable="false">{{ $user->phone ?? '-' }}</div>
    </div>
  </div>
  <button class="btn-save" id="saveBtn" style="display:none;">Simpan Perubahan</button>
</div>

<!-- Alamat / Location -->
<div class="alamat-card">
  <h3>Alamat</h3>
  <form id="alamatForm">
    @csrf
    @method('PUT')
    <div class="alamat-input-wrapper">
      <i class='bx bx-map'></i>
      <input type="text" name="alamat" id="alamatInput"
             value="{{ old('alamat', $user->alamat) }}"
             placeholder="Masukkan alamat lengkap Anda" required>
    </div>
    <div class="alamat-actions">
      <button type="submit" class="btn-save">Simpan Alamat</button>
    </div>
  </form>
  <p id="alamatStatus" style="margin-top:8px;font-size:0.85rem;"></p>
</div>


      <!-- Pesanan Saya -->
      <div class="order-section">
        <h3>Pesanan Saya</h3>
        <div class="order-cards">
          <div class="order-card" onclick="openModal('modalDiproses')">
            <i class='bx bx-package'></i><span>Diproses</span>
          </div>
          <div class="order-card" onclick="openModal('modalRiwayat')">
            <i class='bx bx-receipt'></i><span>Riwayat</span>
          </div>
          <div class="order-card" onclick="openModal('modalRating')">
            <i class='bx bx-star'></i><span>Komentar & Rating</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- MODALS -->

    <!-- Diproses Modal -->
<div class="modal" id="modalDiproses">
  <div class="modal-content slide-up">
    <span class="close" onclick="closeModal('modalDiproses')">&times;</span>
    <h3>Pesanan Sedang Diproses</h3>
    <div class="modal-body">
      @if($ordersProcessing->count())
        @foreach($ordersProcessing as $order)
          @foreach($order->orderItems as $item)
            <div class="order-item">
              <div class="item-thumb">
                <img src="{{ Storage::url(is_array($item->product->gambar_produk) ? $item->product->gambar_produk[0] : $item->product->gambar_produk) }}" 
                     alt="{{ $item->product->nama_produk }}">
              </div>
              <div class="item-info">
                <div class="item-name">
                  {{ $item->product->nama_produk }}
                  @php
                    $status = $order->status_pesanan;
                    $badgeColor = match($status) {
                      'pending' => 'bg-yellow-500',
                      'diproses' => 'bg-blue-500',
                      'dikirim' => 'bg-indigo-500',
                      default => 'bg-gray-400',
                    };
                  @endphp
                  <span class="status-badge {{ $badgeColor }}">{{ ucfirst($status) }}</span>
                </div>
                <div class="item-variant">Qty: {{ $item->jumlah }}</div>
              </div>
              <div class="item-price">
              <div>
                Rp{{ number_format($item->harga_saat_beli, 0, ',', '.') }} × {{ $item->jumlah }} = 
                <strong>Rp{{ number_format($item->harga_saat_beli * $item->jumlah, 0, ',', '.') }}</strong>
              </div>
              <div class="order-total">
  <strong>Total: Rp{{ number_format($order->total_with_shipping, 0, ',', '.') }}</strong>
</div>

            </div>

            </div>
          @endforeach
        @endforeach
      @else
        <p>Tidak ada pesanan yang sedang diproses.</p>
      @endif
    </div>
  </div>
</div>

<!-- Riwayat Pembelian Modal -->
<div class="modal" id="modalRiwayat">
  <div class="modal-content slide-up">
    <span class="close" onclick="closeModal('modalRiwayat')">&times;</span>
    <h3>Riwayat Pembelian</h3>
    <div class="modal-body">
      @if($ordersCompleted->count())
        @foreach($ordersCompleted as $order)
          @foreach($order->orderItems as $item)
            <div class="order-item">
              <div class="item-thumb">
                <img src="{{ Storage::url(is_array($item->product->gambar_produk) ? $item->product->gambar_produk[0] : $item->product->gambar_produk) }}" 
                     alt="{{ $item->product->nama_produk }}">
              </div>
              <div class="item-info">
                <div class="item-name">
                  {{ $item->product->nama_produk }}
                  <span class="status-badge bg-green-500">Selesai</span>
                </div>
                <div class="item-variant">Qty: {{ $item->jumlah }}</div>
              </div>
              <div class="item-price">
              <div>
                Rp{{ number_format($item->harga_saat_beli, 0, ',', '.') }} × {{ $item->jumlah }} = 
                <strong>Rp{{ number_format($item->harga_saat_beli * $item->jumlah, 0, ',', '.') }}</strong>
              </div>
              <div class="order-total">
  <strong>Total: Rp{{ number_format($order->total_with_shipping, 0, ',', '.') }}</strong>
</div>

            </div>

            </div>
          @endforeach
        @endforeach
      @else
        <p>Belum ada pesanan selesai.</p>
      @endif
    </div>
  </div>
</div>

    <!-- Example Modal (Komentar & Rating) -->
<div class="modal" id="modalRating">
  <div class="modal-content slide-up">
    <span class="close" onclick="closeModal('modalRating')">&times;</span>
    <h3>Komentar & Rating</h3>

    <div class="modal-body">
      @if($ratings->count())
        @foreach($ratings as $rating)
          <div class="rating-item">
            <strong>{{ $rating->product ? $rating->product->nama_produk : 'Aplikasi' }}</strong>

            <!-- Dynamic Stars -->
            <div class="stars">
              @for ($i = 1; $i <= $rating->bintang; $i++)
                &#9733;
              @endfor
              @for ($i = $rating->bintang + 1; $i <= 5; $i++)
                <span style="color:#ddd">&#9733;</span>
              @endfor
            </div>

            <p>{{ $rating->ulasan }}</p>
          </div>
        @endforeach
      @else
        <p>Belum ada komentar atau rating.</p>
      @endif
    </div>
  </div>
</div>

<!-- Logout Confirmation Modal -->
<div class="modal" id="logoutModal">
  <div class="modal-content logout-warning slide-up">
    <h3>Konfirmasi Logout</h3>
    <p>Apakah Anda yakin ingin keluar dari akun?</p>
    <div class="modal-actions">
      <button onclick="closeModal('logoutModal')" class="btn-cancel">
        Batal
      </button>
      <button onclick="document.getElementById('logoutForm').submit()" class="btn-logout">
        Logout
      </button>
    </div>
  </div>
</div>




    <div id="toast" class="toast"></div>

  
  
  <script>
// === Toast notifications ===
function showToast(message, type = "success") {
  const toast = document.getElementById("toast");
  toast.textContent = message;
  toast.className = `toast toast-${type}`;
  toast.style.display = "block";

  setTimeout(() => {
    toast.style.display = "none";
  }, 3000);
}

// === Modal handling ===
function openModal(id) {
  document.getElementById(id).style.display = "block";
}

function closeModal(id) {
  document.getElementById(id).style.display = "none";
}

window.onclick = function (event) {
  document.querySelectorAll(".modal").forEach((modal) => {
    if (event.target == modal) modal.style.display = "none";
  });
};

// === Avatar upload preview + auto-submit ===
const avatarInput = document.getElementById("avatar-upload");
const avatarPreview = document.getElementById("avatar-preview");

avatarInput?.addEventListener("change", function () {
  const file = this.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => (avatarPreview.src = e.target.result);
    reader.readAsDataURL(file);
    this.form.submit();
  }
});

// === Inline profile editing (name, email, phone) ===
const editBtn = document.getElementById("editBtn");
const saveBtn = document.getElementById("saveBtn");
const fields = ["nameField", "emailField", "phoneField"].map((id) =>
  document.getElementById(id)
);

editBtn?.addEventListener("click", () => {
  fields.forEach((f) => f.setAttribute("contenteditable", "true"));
  editBtn.style.display = "none";
  saveBtn.style.display = "inline-block";
});

saveBtn?.addEventListener("click", () => {
  const data = {
    name: fields[0].innerText.trim(),
    email: fields[1].innerText.trim(),
    phone: fields[2].innerText.trim(),
  };

  fetch("{{ route('user.profile.update') }}", {
    method: "PUT",
    headers: {
      "Content-Type": "application/json",
      "X-CSRF-TOKEN": "{{ csrf_token() }}",
    },
    body: JSON.stringify(data),
  })
    .then(async (res) => {
      if (!res.ok) throw await res.json();
      return res.json();
    })
    .then(() => {
      showToast("✅ Profil berhasil diperbarui!", "success");
      fields.forEach((f) => f.setAttribute("contenteditable", "false"));
      editBtn.style.display = "inline-block";
      saveBtn.style.display = "none";
    })
    .catch(() => {
      showToast("❌ Perubahan gagal! Mohon coba lagi.", "error");
    });
});

// === Alamat saving (separate form) ===
const alamatForm = document.getElementById("alamatForm");
alamatForm?.addEventListener("submit", (e) => {
  e.preventDefault();

  const data = {
    alamat: document.getElementById("alamatInput").value.trim(),
  };

  fetch("{{ route('user.profile.update') }}", {
    method: "PUT",
    headers: {
      "Content-Type": "application/json",
      "X-CSRF-TOKEN": "{{ csrf_token() }}",
    },
    body: JSON.stringify(data),
  })
    .then(async (res) => {
      if (!res.ok) throw await res.json();
      return res.json();
    })
    .then(() => {
      showToast("✅ Alamat berhasil diperbarui!", "success");
    })
    .catch(() => {
      showToast("❌ Gagal menyimpan alamat.", "error");
    });
});

// === Logout Confirmation ===
const logoutBtn = document.getElementById("logoutBtn");
logoutBtn?.addEventListener("click", () => {
  openModal("logoutModal");
});
</script>


</body>
</html>
