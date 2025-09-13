<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kategori | PawMart</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin/kategori.css') }}">
</head>
<body>
<div class="dashboard-wrapper">
  @include('partials.sidebar')

  <main class="main-content">
    <div class="top-navbar" align="center">
      <h2>Kategori Produk</h2>
    </div>

    <div style="text-align:center;">
      <button class="tambah-btn" id="openTambahModal">Tambah Kategori</button>
    </div>

    <table class="clean-table" align="center">
      <thead>
        <tr>
          <th>Kategori</th>
          <th>Banyak Produk</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($categories as $category)
        <tr data-id="{{ $category->id }}">
          <td class="nama-kategori">{{ $category->nama_kategori }}</td>
          <td>{{ $category->products_count }}</td>
          <td class="table-actions">
            <button class="icon-btn edit" title="Edit"><i class='bx bx-edit-alt'></i></button>
            <button class="icon-btn delete" title="Hapus"><i class='bx bx-trash'></i></button>
          </td>
        </tr>
        @empty
        <tr><td colspan="3">Belum ada kategori.</td></tr>
        @endforelse
      </tbody>
    </table>
  </main>
</div>

<!-- Tambah Modal -->
<div class="modal" id="tambahModal">
  <div class="modal-content">
    <h3>Tambah Kategori</h3>
    <form id="tambahForm" method="POST" action="{{ route('admin.category.store') }}">
      @csrf
      <input type="text" name="nama_kategori" placeholder="Nama Kategori" required>
      <div class="btn-group">
        <button type="button" class="btn-cancel" id="closeTambahModal">Batal</button>
        <button type="submit" class="btn-save">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal" id="editModal">
  <div class="modal-content">
    <h3>Edit Kategori</h3>
    <form id="editForm" method="POST">
      @csrf
      @method('PUT')
      <input type="text" name="nama_kategori" id="editKategoriInput" placeholder="Nama Kategori" required>
      <div class="btn-group">
        <button type="button" class="btn-cancel" id="closeEditModal">Batal</button>
        <button type="submit" class="btn-save">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Delete Modal -->
<div class="modal" id="deleteModal">
  <div class="modal-content">
    <h3>Hapus Kategori</h3>
    <p>Apakah Anda yakin ingin menghapus kategori ini?</p>
    <form id="deleteForm" method="POST">
      @csrf
      @method('DELETE')
      <div class="btn-group">
        <button type="button" class="btn-cancel" id="closeDeleteModal">Batal</button>
        <button type="submit" class="btn-delete">Hapus</button>
      </div>
    </form>
  </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="success-modal">
  <div class="success-modal-content">
    <div class="success-icon"><i class='bx bx-check-circle'></i></div>
    <h3 id="successMessage"></h3>
  </div>
</div>

<script>
  const CATEGORY_BASE_URL = "{{ url('/admin/category') }}";
  const CATEGORY_SUCCESS = @json(session('success'));
</script>
<script src="{{ asset('js/sidebar.js') }}"></script>
<script src="{{ asset('js/kategori.js') }}"></script>
</body>
</html>
