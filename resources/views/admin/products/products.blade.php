<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Produk | PawMart Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Boxicons -->
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

  <!-- CSS -->
  <link rel="stylesheet" href="{{ asset('css/admin/adminproduct.css') }}"/>
  <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}"/>
  <link rel="stylesheet" href="{{ asset('css/adminnavbar.css') }}"/>

  
</head>
<body>
<div class="dashboard-wrapper">
  <!-- Sidebar -->
  @include('partials.sidebar')

  <!-- Main Content -->
  <main class="main-content">
    @include('partials.adminnavbar')

    <div class="products-header">
      <h3>Daftar Produk</h3>
      <a href="{{ route('admin.products.create') }}" class="btn-add">+ Tambah Produk</a>
    </div>

    <!-- Category Dropdown -->
    <div class="category-filter">
      <label for="categorySelect">Filter Kategori:</label>
      <select id="categorySelect">
        <option value="makanan kucing" selected>Makanan Kucing</option>
        <option value="makanan anjing">Makanan Anjing</option>
        <option value="mainan">Mainan</option>
        <option value="obat">Obat</option>
        <option value="peralatan & perawatan">Peralatan</option>
      </select>
    </div>

    <div class="products-grid">
      @forelse($products->sortBy('nama_produk') as $product)
        @php
          $category = strtolower(trim($product->category->nama_kategori ?? 'uncategorized'));
        @endphp
        <div class="product-card" data-category="{{ $category }}">
          <img src="{{ !empty($product->gambar_produk) ? asset('storage/'.$product->gambar_produk[0]) : asset('asset/placeholder.png') }}" alt="{{ $product->nama_produk }}">
          <h4>{{ $product->nama_produk }}</h4>
          <p class="price">Rp{{ number_format($product->harga_produk, 0, ',', '.') }}</p>
          <div class="product-actions">
            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn-edit">
              <i class='bx bx-edit-alt'></i> Edit
            </a>
            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="delete-form" style="display:inline">
              @csrf
              @method('DELETE')
              <button type="button" class="btn-delete" onclick="openDeleteModal(this)" data-product-name="{{ $product->nama_produk }}">
                <i class='bx bx-trash'></i> Hapus
              </button>
            </form>
          </div>
        </div>
      @empty
        <div class="empty-state">
          <i class='bx bx-info-circle'></i>
          <p>Tidak ada produk saat ini.<br> Klik <strong>"Tambah Produk"</strong> untuk menambahkan.</p>
        </div>
      @endforelse
    </div>

    <!-- DELETE MODAL -->
    <div id="deleteModal" class="modal">
      <div class="modal-content">
        <h3>Hapus Produk?</h3>
        <p>Apakah Anda yakin ingin menghapus <span id="modalProductName"></span>? Tindakan ini tidak dapat dibatalkan.</p>
        <div class="modal-buttons">
          <button class="cancel-btn" onclick="closeModal()">Batal</button>
          <button class="confirm-btn" id="confirmDeleteBtn">Hapus</button>
        </div>
      </div>
    </div>
  </main>
</div>

<script src="{{ asset('js/sidebar.js') }}"></script>
<script>
// Delete Modal
let activeForm = null;
function openDeleteModal(button) {
    document.getElementById("modalProductName").textContent = button.dataset.productName;
    activeForm = button.closest('form');
    document.getElementById("deleteModal").style.display = "flex";
}
function closeModal() {
    document.getElementById("deleteModal").style.display = "none";
    activeForm = null;
}
document.getElementById("confirmDeleteBtn").addEventListener("click", function() {
    if(activeForm) activeForm.submit();
});

// Category filter
const categorySelect = document.getElementById('categorySelect');
const productCards = document.querySelectorAll('.product-card');

function filterCategory(category) {
    category = category.toLowerCase().trim();
    let anyVisible = false;
    productCards.forEach(card => {
        const cardCategory = card.dataset.category.toLowerCase().trim();
        if(cardCategory === category){
            card.style.display = 'block';
            anyVisible = true;
        } else {
            card.style.display = 'none';
        }
    });
}

filterCategory('makanan kucing'); // Default category

categorySelect.addEventListener('change', function() {
    filterCategory(this.value);
});
</script>
</body>
</html>
