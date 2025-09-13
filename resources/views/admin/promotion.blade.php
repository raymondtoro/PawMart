<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Promosi | PawMart</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Boxicons -->
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin/promosi.css') }}">
</head>
<body>
<div class="dashboard-wrapper">
  @include('partials.sidebar')

  <main class="main-content">
    <div class="top-navbar" align="center">
      <h2>Promosi Produk</h2>
    </div>

    <div style="text-align:center; margin-bottom:1rem;">
      <button class="tambah-btn" id="openTambahModal">Tambah Promosi</button>
    </div>

    <table class="clean-table" align="center">
      <thead>
        <tr>
          <th>Judul Promosi</th>
          <th>Deskripsi</th>
          <th>Diskon</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($promotions as $promotion)
        <tr>
          <td>{{ $promotion->judul_promosi }}</td>
          <td>{{ $promotion->deskripsi_promosi ?? '-' }}</td>
          <td>{{ $promotion->diskon !== null ? $promotion->diskon . '%' : '-' }}</td>
          <td class="table-actions">
            <button class="icon-btn edit" onclick="openEditModal(
              {{ $promotion->id }},
              '{{ addslashes($promotion->judul_promosi) }}',
              '{{ addslashes($promotion->deskripsi_promosi ?? '') }}',
              '{{ $promotion->diskon ?? '' }}'
            )">
              <i class='bx bx-edit-alt'></i>
            </button>

            <button class="icon-btn delete" onclick="openDeleteModal({{ $promotion->id }})">
              <i class='bx bx-trash'></i>
            </button>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="4" style="text-align:center;">Belum ada promosi</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </main>
</div>

<!-- Tambah Modal -->
<div class="modal" id="tambahModal">
  <div class="modal-content">
    <h3>Tambah Promosi</h3>
    <form action="{{ route('admin.promotion.store') }}" method="POST">
      @csrf
      <input type="text" name="judul_promosi" placeholder="Judul Promosi" required>
      <input type="text" name="deskripsi_promosi" placeholder="Deskripsi Promosi">
      <input type="text" name="diskon" placeholder="Diskon (%)">
      <div class="btn-group">
        <button type="button" class="btn-cancel" onclick="closeModal('tambahModal')">Batal</button>
        <button type="submit" class="btn-save">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal" id="editModal">
  <div class="modal-content">
    <h3>Edit Promosi</h3>
    <form id="editForm" method="POST">
      @csrf
      @method('PUT')
      <input type="text" name="judul_promosi" id="editJudulInput" placeholder="Judul Promosi" required>
      <input type="text" name="deskripsi_promosi" id="editDeskripsiInput" placeholder="Deskripsi Promosi">
      <input type="text" name="diskon" id="editDiskonInput" placeholder="Diskon (%)">
      <div class="btn-group">
        <button type="button" class="btn-cancel" onclick="closeModal('editModal')">Batal</button>
        <button type="submit" class="btn-save">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Delete Modal -->
<div class="modal" id="deleteModal">
  <div class="modal-content">
    <h3>Hapus Promosi</h3>
    <p>Apakah Anda yakin ingin menghapus promosi ini?</p>
    <form id="deleteForm" method="POST">
      @csrf
      @method('DELETE')
      <div class="btn-group">
        <button type="button" class="btn-cancel" onclick="closeModal('deleteModal')">Batal</button>
        <button type="submit" class="btn-delete" id="confirmDeleteBtn">Hapus</button>
      </div>
    </form>
  </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="success-modal">
  <div class="success-modal-content">
    <div class="success-icon">
      <i class='bx bx-check-circle'></i>
    </div>
    <h3 id="successMessage"></h3>
  </div>
</div>

<script>
  const BASE_URL = "{{ url('/admin/promotion') }}";
  const PROMO_SUCCESS = @json(session('success'));
</script>

<script src="{{ asset('js/promosi.js') }}"></script>
<script src="{{ asset('js/sidebar.js') }}"></script>
</body>
</html>
