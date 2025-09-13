<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Tambah Produk - PawMart</title>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin/add_page.css') }}"/>
  <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}"/>
  <link rel="stylesheet" href="{{ asset('css/adminnavbar.css') }}">

</head>
<body>
<div class="dashboard-wrapper">
  @include('partials.sidebar')

  <!-- Main Content -->
        <main class="main-content">
            @include('partials.adminnavbar')


    <div class="content">
      <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="product-flex-container">
          <!-- Foto Produk -->
          <section class="card photos">
            <h2>Foto Produk</h2>
            <div class="photo-grid">
              @for($i = 0; $i < 4; $i++)
                <div class="photo-slot">
                  <i class='bx bx-image-add upload-icon'></i>
                  <img src="{{ asset('asset/pawmart produk.png') }}" alt="Foto Produk {{ $i+1 }}">
                  <input type="file" name="gambar_produk[]" accept="image/*">
                  <button type="button" class="delete-btn">&times;</button>
                </div>
              @endfor
            </div>
            <small style="color: gray; font-size: 0.9rem;">
              Maksimal 4 foto. JPG, JPEG, PNG — maksimum 5MB per file
            </small>
          </section>

          <!-- Detail Produk -->
          <section class="card product-detail">
            <h2>Detail Produk</h2>

            <input id="nama" name="nama_produk" type="text" placeholder="Nama Produk" required/>
            
            <select id="kategori" name="kategori_id" required>
              <option disabled selected>Pilih kategori</option>
              @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->nama_kategori }}</option>
              @endforeach
            </select>

            <input id="stok" name="stok_produk" type="number" min="0" placeholder="Jumlah Stok" required/>

            <div class="input-prefix">
              <span class="prefix">Rp</span>
              <input id="harga" name="harga_produk" type="text" placeholder="Harga" min="0" step="1" required/>
            </div>

            <textarea id="deskripsi" name="deskripsi_produk" rows="4" placeholder="Deskripsi Produk"></textarea>

            <select name="promosi_id">
              <option value="">Promosi (Opsional)</option>
              @foreach($promotions as $promo)
                <option value="{{ $promo->id }}">{{ $promo->judul_promosi }}</option>
              @endforeach
            </select>

            <div class="form-actions" style="text-align: center;">
              <button type="submit" class="btn primary">
                <span class="rocket">🚀</span> Luncurkan Produk
              </button>
            </div>
          </section>
        </div>
      </form>
    </div>
  </main>
</div>

<script>
document.querySelectorAll('.photo-slot').forEach(slot => {
  const input = slot.querySelector('input[type="file"]');
  const img = slot.querySelector('img');
  const delBtn = slot.querySelector('.delete-btn');
  const defaultSrc = img.src;

  // Preview
  input.addEventListener('change', e => {
    const file = e.target.files[0];
    if (file && file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = evt => {
        img.src = evt.target.result;
        slot.classList.add('has-image');
      };
      reader.readAsDataURL(file);
    }
  });

  // Delete photo
  delBtn.addEventListener('click', () => {
    img.src = defaultSrc; // reset preview
    input.value = ""; // clear input
    slot.classList.remove('has-image');
  });
});
</script>
</body>
</html>
