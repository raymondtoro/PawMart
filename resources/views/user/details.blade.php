<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Detail Produk Petshop</title>
<link rel="stylesheet" href="{{ asset('css/user/details.css') }}">
<link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
<style>
/* Floating notification */
#notification {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background: rgb(181,62,205);
  color: white;
  padding: 12px 20px;
  border-radius: 8px;
  display: none;
  box-shadow: 0 4px 6px rgba(0,0,0,0.2);
  z-index: 9999;
  font-weight: bold;
}
.star-rating i { font-size: 24px; cursor: pointer; color: #ccc; transition: color 0.2s; }
.star-rating i.hover, .star-rating i.selected { color: #f4b400; }
</style>
</head>
<body>
@include('partials.navbar')

@php
$reviews = $product->ratings ?? collect([]);
$averageRating = $reviews->count() ? $reviews->avg('bintang') : 0;
$totalReviews = $reviews->count();
$roundedRating = round($averageRating);
$images = is_array($product->gambar_produk) ? $product->gambar_produk : json_decode($product->gambar_produk,true) ?? [];
$mainImage = !empty($images) ? Storage::url($images[0]) : asset('images/default-product.png');
@endphp

<div class="container">

  <!-- PRODUCT PAGE -->
  <div class="product-page fade-slide">
    <!-- LEFT: Images -->
    <div class="product-images">
      <img src="{{ $mainImage }}" id="mainImage" class="main-image">
      <div class="thumbnail-images">
        @forelse($images as $img)
          <img src="{{ Storage::url($img) }}" data-img="{{ Storage::url($img) }}">
        @empty
          <img src="{{ asset('images/default-product.png') }}" data-img="{{ asset('images/default-product.png') }}">
        @endforelse
      </div>
    </div>

    <!-- RIGHT: Details -->
    <div class="product-details fade-slide">
      <h1>{{ $product->nama_produk ?? 'Produk Tidak Diketahui' }}</h1>

      <!-- Rating -->
      <div class="rating">
        @for($i=1;$i<=5;$i++)
          @if($i <= $roundedRating) <i class='bx bxs-star'></i>
          @else <i class='bx bx-star'></i> @endif
        @endfor
        ({{ number_format($averageRating,1) }}) | {{ $totalReviews }} Ulasan
      </div>

      @php
    $originalPrice = $product->harga_produk ?? 0;
    $promotion = $product->promotion ?? null; // assuming you have a relation to promotions
    $discountedPrice = $promotion ? $originalPrice * (1 - $promotion->diskon / 100) : null;
@endphp

<h2 class="price">
    @if($promotion && $discountedPrice < $originalPrice)
        <span style="text-decoration: line-through; color: #888; font-size: 1rem;">
            Rp{{ number_format($originalPrice, 0, ',', '.') }}
        </span>
        <span style="color: #e74c3c; font-weight: bold; font-size: 1.2rem;">
            Rp{{ number_format($discountedPrice, 0, ',', '.') }}
        </span>
    @else
        Rp{{ number_format($originalPrice, 0, ',', '.') }}
    @endif
</h2>


      <p class="description">{{ $product->deskripsi_produk ?? 'Deskripsi produk tidak tersedia.' }}</p>

      <div class="buttons">
        <!-- Add to Cart Button -->
        @php
$images = is_array($product->gambar_produk) ? $product->gambar_produk : json_decode($product->gambar_produk,true) ?? [];
$mainImage = !empty($images) ? Storage::url($images[0]) : asset('images/default-product.png');

// Original price
$originalPrice = $product->harga_produk ?? 0;

// Check if product has discount
$hasDiscount = isset($product->diskon) && $product->diskon > 0;

// Final price considering discount
$finalPrice = $hasDiscount ? $originalPrice * (1 - $product->diskon / 100) : $originalPrice;
@endphp




<!-- Quantity Selector -->
<div class="quantity-selector">
    <label for="qty">Jumlah:</label>
    <input type="number" id="qty" name="qty" value="1" min="1" max="{{ $product->stok ?? 100 }}">
</div>

<form id="buyNowForm" method="POST" action="{{ route('user.transaction.buy') }}" style="display:inline-block;">
    @csrf
    <input type="hidden" name="produk_id" value="{{ $product->id }}">
    <input type="hidden" name="qty" id="buyQty" value="1">
    <button type="submit" class="buy">
        <i class='bx bx-cart'></i> Beli Sekarang
    </button>
</form>

<button class="add-cart" 
        data-id="{{ $product->id }}" 
        data-name="{{ $product->nama_produk }}" 
        data-price="{{ $finalPrice }}"  
        data-image="{{ $mainImage }}">
    <i class='bx bx-cart-add'></i> Tambah ke Keranjang
</button>


      </div>

      <div class="extra-info">
        <p><i class='bx bx-truck'></i> {{ $product->info_ongkir ?? 'Gratis Ongkir - Min. belanja Rp 200.000' }}</p>
        <p><i class='bx bx-shield'></i> {{ $product->info_garansi ?? 'Garansi & Pengembalian hingga 7 hari' }}</p>
      </div>
    </div>
  </div>

  <!-- TABS: Reviews & Related -->
  <div class="tabs-section">
    <div class="tabs">
      <div class="tab active" data-tab="reviews">Ulasan</div>
      <div class="tab" data-tab="related">Produk Sejenis</div>
    </div>

    <!-- Reviews Tab -->
    <div class="tab-content active" id="reviews">
      @forelse($reviews as $review)
        <div class="review">
          <div class="reviewer">{{ $review->user->name ?? 'Anonim' }}</div>
          <div class="stars">
            @for($i=1;$i<=5;$i++)
              @if($i <= $review->bintang) <i class='bx bxs-star'></i>
              @else <i class='bx bx-star'></i> @endif
            @endfor
          </div>
          <p>{{ $review->ulasan }}</p>
        </div>
      @empty
        <p>Belum ada ulasan untuk produk ini.</p>
      @endforelse
      <button id="openReviewModal" class="btn-review"><i class='bx bx-pencil'></i> Beri Ulasan</button>
    </div>

<!-- Related Products Tab -->
<div class="tab-content" id="related">
    <div class="related-products">
        @foreach($relatedProducts->take(4) as $rel)
            @php
                // Get product images
                $relImages = is_array($rel->gambar_produk) ? $rel->gambar_produk : json_decode($rel->gambar_produk,true) ?? [];
                $firstRelImage = !empty($relImages) ? Storage::url($relImages[0]) : asset('images/default-product.png');

                // Original price
                $originalPrice = $rel->harga_produk ?? 0;

                // Check if product has discount
                // Assuming $rel->diskon is a percentage (like 20 for 20%)
                $hasDiscount = isset($rel->diskon) && $rel->diskon > 0;

                // Calculate discounted price if there is a discount
                $finalPrice = $hasDiscount ? $originalPrice * (1 - $rel->diskon / 100) : $originalPrice;
            @endphp

            <a href="{{ route('user.details',$rel->id) }}" class="related-card">
                <img src="{{ $firstRelImage }}" alt="{{ $rel->nama_produk }}">
                <h4>{{ $rel->nama_produk }}</h4>
                <div class="price">
                    @if($hasDiscount)
                        <span style="text-decoration: line-through; color: #888;">
                            Rp {{ number_format($originalPrice,0,',','.') }}
                        </span>
                        <span style="color: #e74c3c; font-weight: bold;">
                            Rp {{ number_format($finalPrice,0,',','.') }}
                        </span>
                    @else
                        Rp {{ number_format($originalPrice,0,',','.') }}
                    @endif
                </div>
            </a>
        @endforeach
    </div>
</div>
  </div>

  <!-- REVIEW MODAL -->
  <div id="reviewModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <form action="{{ route('user.rating') }}" method="POST" id="reviewFormModal">
        @csrf
        <input type="hidden" name="produk_id" value="{{ $product->id }}">
        <input type="hidden" name="bintang" id="bintangInputModal" value="0">
        <div class="star-rating" id="starRatingModal">
          @for($i=1;$i<=5;$i++)
            <i class='bx bx-star' data-value="{{ $i }}"></i>
          @endfor
        </div>
        <textarea name="ulasan" rows="3" placeholder="Tulis ulasan Anda..." required></textarea>
        
        <button type="submit">Kirim Ulasan</button>
      </form>
    </div>
  </div>
</div>

<!-- Alamat Warning Modal -->
<div id="alamatModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h3>Alamat Belum Terisi</h3>
    <p>Silakan lengkapi alamat Anda sebelum melanjutkan ke checkout.</p>
    <a href="{{ route('user.profile') }}" class="btn">Isi Alamat</a>
  </div>
</div>


<!-- Notification -->
<div id="notification "></div>

<script>
  document.addEventListener("DOMContentLoaded", () => {
    @if(session('warning'))
    const modal = document.getElementById('alamatModal');
    const closeBtn = modal.querySelector('.close');

    modal.style.display = 'block';
    closeBtn.addEventListener('click', ()=> modal.style.display='none');
    window.addEventListener('click', e => { if(e.target==modal) modal.style.display='none'; });
    @endif
});

document.addEventListener("DOMContentLoaded", () => {
  const mainImage = document.getElementById("mainImage");
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

  // ===== Thumbnail click swap =====
  document.querySelectorAll(".thumbnail-images img").forEach(img => {
    img.addEventListener("click", () => mainImage.src = img.dataset.img);
  });

  // ===== Fade-slide animations =====
  document.querySelectorAll('.fade-slide').forEach((el,i) => setTimeout(() => el.classList.add('show'), i*200));
  document.querySelectorAll('.thumbnail-images img').forEach((img,i) => setTimeout(() => img.classList.add('show'), i*150));

  // ===== Tabs functionality =====
  const tabs = document.querySelectorAll('.tab');
  const contents = document.querySelectorAll('.tab-content');
  tabs.forEach(tab => tab.addEventListener('click', () => {
    tabs.forEach(t => t.classList.remove('active'));
    tab.classList.add('active');
    contents.forEach(c => c.id === tab.dataset.tab ? c.classList.add('active') : c.classList.remove('active'));
  }));

  // ===== Add to Cart (DATABASE, not localStorage) =====
  document.querySelectorAll('.add-cart').forEach(btn => {
    btn.addEventListener('click', async () => {
      const productId = btn.dataset.id;
      const productName = btn.dataset.name;

      try {
        const response = await fetch("/user/cart/add", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
            "X-Requested-With": "XMLHttpRequest"
          },
          body: JSON.stringify({ produk_id: productId })
        });

        const data = await response.json();

        // ✅ Show floating toast
        const notif = document.createElement("div");
        notif.className = "cart-notif";
        notif.textContent = data.message || `${productName} berhasil ditambahkan ke keranjang 🛒`;
        document.body.appendChild(notif);

        void notif.offsetWidth; // force reflow
        notif.classList.add('show');
        setTimeout(() => {
          notif.classList.remove('show');
          setTimeout(() => notif.remove(), 300);
        }, 1500);

        // ✅ Update badge
        if (typeof updateQtyBadges === 'function') updateQtyBadges();
      } catch (err) {
        console.error("❌ Gagal tambah produk:", err);
        alert("Terjadi kesalahan saat menambahkan produk ke keranjang.");
      }
    });
  });

  // quantity-selector
  const qtyInput = document.getElementById('qty');
const buyQtyInput = document.getElementById('buyQty');
qtyInput.addEventListener('input', () => buyQtyInput.value = qtyInput.value);


  // ===== Review Modal =====
  const modal = document.getElementById("reviewModal");
  const openBtn = document.getElementById("openReviewModal");
  const closeBtn = modal.querySelector(".close");
  const stars = document.querySelectorAll('#starRatingModal i');
  const bintangInput = document.getElementById('bintangInputModal');

  if(openBtn && modal){
    openBtn.addEventListener('click', ()=>modal.style.display='block');
    closeBtn.addEventListener('click', ()=>modal.style.display='none');
    window.addEventListener('click', e=>{if(e.target==modal) modal.style.display='none';});

    stars.forEach(star=>{
      star.addEventListener('mouseover', ()=>{
        const val=parseInt(star.dataset.value);
        stars.forEach(s=>s.classList.toggle('hover', parseInt(s.dataset.value)<=val));
      });
      star.addEventListener('mouseout', ()=>stars.forEach(s=>s.classList.remove('hover')));
      star.addEventListener('click', ()=>{
        const val=parseInt(star.dataset.value);
        bintangInput.value = val;
        stars.forEach(s=>s.classList.toggle('selected', parseInt(s.dataset.value)<=val));
      });
    });
  }
});
</script>


</body>
</html>
