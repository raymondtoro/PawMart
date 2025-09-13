<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PawMart - Admin Profil</title>

  <!-- Fonts & Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

  <!-- Styles -->
  <link rel="stylesheet" href="{{ asset('css/admin/adminprofile.css') }}">
  <link rel="stylesheet" href="{{ asset('css/adminnavbar.css') }}">

  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

  <div class="container">
      <!-- Back Button -->
  <a href="{{ route('admin.dashboard') }}" class="back-btn">
    <i class='bx bx-arrow-back'></i> Kembali
  </a>
    <!-- Navbar -->
    @include('partials.adminnavbar')

    <body>

  <div class="container">
    <div class="profile-card">
      <!-- Profile Header -->
      <div class="profile-header">
        <!-- Avatar -->
        <div class="avatar">
          <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <label for="avatar-upload">
              <img id="avatar-preview" 
                   src="{{ $admin->avatar ? asset('storage/' . $admin->avatar) : asset('asset/default-avatar.png') }}" 
                   alt="Avatar">
              <div class="overlay"><i class='bx bx-camera'></i></div>
            </label>
            <input type="file" name="avatar" id="avatar-upload" accept="image/*" hidden>
          </form>
        </div>

        <!-- Admin Info -->
        <div class="admin-details">
          <h2 style="margin:0;">{{ $admin->name }}</h2>
          <p style="margin:4px 0; color:#6b7280;">Selamat datang di Profil Admin!</p>

          
        </div>
      </div>


      <!-- Profile Info -->
      <div class="info-card">
        <h3>Info Admin</h3>
        <button class="edit-btn" id="editBtn">Edit</button>

        <div class="info-grid" id="infoGrid">
          <div class="info-item">
            <span>Nama</span>
            <div id="nameField" contenteditable="false">{{ $admin->name }}</div>
          </div>
          <div class="info-item">
            <span>Email</span>
            <div id="emailField" contenteditable="false">{{ $admin->email }}</div>
          </div>
          <div class="info-item">
            <span>No. Handphone</span>
            <div id="phoneField" contenteditable="false">{{ $admin->phone ?? '-' }}</div>
          </div>
          <div class="info-item">
            <span>Role</span>
            <div id="roleField" contenteditable="false">{{ $admin->role }}</div>
          </div>
        </div>

        <button class="btn-save" id="saveBtn" style="display:none;">Simpan Perubahan</button>
      </div>
    </div>
  </div>

  <!-- Toast -->
  <div id="toast" class="toast"></div>

  <!-- Scripts -->
  <script>
  // === Toast Notification ===
  function showToast(message, type = "success") {
    const toast = document.getElementById("toast");
    toast.textContent = message;
    toast.className = `toast toast-${type}`;
    toast.style.display = "block";
    setTimeout(() => { toast.style.display = "none"; }, 3000);
  }

  // === Avatar Upload ===
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

  // === Inline Editing ===
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

    fetch("{{ route('admin.profile.update') }}", {
  method: "PUT",
  headers: {
    "Content-Type": "application/json",
    "X-CSRF-TOKEN": "{{ csrf_token() }}",
    "X-Requested-With": "XMLHttpRequest" // 👈 ADD THIS
  },
  body: JSON.stringify(data),
})
  .then(res => res.json())   // no need for res.ok check
  .then((result) => {
    if (result.success) {
      showToast("✅ Berhasil diperbarui!", "success");
      fields.forEach((f) => f.setAttribute("contenteditable", "false"));
      editBtn.style.display = "inline-block";
      saveBtn.style.display = "none";
    } else {
      showToast("❌ Perubahan Gagal!", "error");
    }
  })
  .catch(() => {
    showToast("❌ Terjadi kesalahan server!", "error");
  });

  });
</script>

</body>
</html>
