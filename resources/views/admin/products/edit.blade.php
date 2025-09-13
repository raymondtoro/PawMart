<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit Produk - PawMart</title>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin/add_page.css') }}"/>
  <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}"/>
  <link rel="stylesheet" href="{{ asset('css/adminnavbar.css') }}">
</head>
<body>
<div class="dashboard-wrapper">
  @include('partials.sidebar')

  <main class="main-content">
    @include('partials.adminnavbar')

    <div class="content">
      <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="product-flex-container">
          <!-- Foto Produk -->
          <section class="card photos">
            <h2>Foto Produk</h2>
            <div class="photo-grid">
              @php
                $images = $product->gambar_produk ?? [];
                $total_slots = max(4, count($images));
              @endphp

              @for($i = 0; $i < $total_slots; $i++)
                <div class="photo-slot {{ isset($images[$i]) ? 'has-image' : '' }}">
                  <i class='bx bx-image-add upload-icon'></i>
                  <img src="{{ isset($images[$i]) ? asset('storage/' . $images[$i]) : asset('asset/pawmart produk.png') }}" alt="Foto Produk {{ $i+1 }}">
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

            <input id="nama" name="nama_produk" type="text" 
                   value="{{ old('nama_produk', $product->nama_produk) }}" 
                   placeholder="Nama Produk" required/>
            
            <select id="kategori" name="kategori_id" required>
              <option disabled>Pilih kategori</option>
              @foreach($categories as $category)
                <option value="{{ $category->id }}" 
                  {{ $product->kategori_id == $category->id ? 'selected' : '' }}>
                  {{ $category->nama_kategori }}
                </option>
              @endforeach
            </select>

            <input id="stok" name="stok_produk" type="number" min="0"
                   value="{{ old('stok_produk', $product->stok_produk) }}" 
                   placeholder="Jumlah Stok" required/>

            <div class="input-prefix">
            <span class="prefix">Rp</span>
            <input id="harga" name="harga_produk" type="number" placeholder="Harga"
                    min="0" step="1"
                    value="{{ old('harga_produk', $product->harga_produk) }}" required/>
            </div>


            <textarea id="deskripsi" name="deskripsi_produk" rows="4" placeholder="Deskripsi Produk">{{ old('deskripsi_produk', $product->deskripsi_produk) }}</textarea>

            <select name="promosi_id">
              <option value="">Promosi (Opsional)</option>
              @foreach($promotions as $promo)
                <option value="{{ $promo->id }}" 
                  {{ $product->promosi_id == $promo->id ? 'selected' : '' }}>
                  {{ $promo->judul_promosi }}
                </option>
              @endforeach
            </select>

            <div class="form-actions" style="text-align: center;">
              <button type="submit" class="btn primary">
                <span class="rocket">💾</span> Simpan Perubahan
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
