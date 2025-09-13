<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PawMart</title>

  <!-- Icons & CSS -->
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/user/userhome.css') }}">
  <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
  <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
</head>
<body>

  <!-- NAVBAR -->
  @include('partials.navbar')

  <!-- HERO -->
  <section class="hero">
    <div class="hero-text">
      <h1>Toko serba ada untuk<br>hewan peliharaan yang<br><span>bahagia dan sehat</span></h1>
      <p>Temukan makanan, aksesoris, dan produk perawatan pilihan di satu tempat yang ramah.</p>
      <a href="{{ route('shop') }}">
        <button>Belanja Sekarang</button>
      </a>
    </div>
    <div class="hero-img">
      <img src="{{ asset('asset/siamese.png') }}" alt="Gambar Hewan">
    </div>
  </section>

  <!-- WEEKEND DEALS (Static Demo) -->
  <section class="deals">
    <h2>Promo Akhir Pekan</h2>
    <p>Buruan! Penawaran terbatas waktu</p>
    <div class="countdown" id="countdown"></div>
    <div class="swiper">
      <div class="swiper-wrapper">
        <div class="swiper-slide">
          <div class="slide-img">
            <img src="{{ asset('asset/foods/cat3.jpg') }}" alt="Makanan Anjing">
          </div>
          <div class="slide-text">
            <h3>Makanan Kucing Premium</h3>
            <p>Makanan sehat & bergizi untuk Kucing kesayangan Anda.</p>
            <div class="price">Rp 78.000</div>
            <a href="#">
              <button>Beli Sekarang →</button>
            </a>
          </div>
        </div>

        <div class="swiper-slide">
          <div class="slide-img">
            <img src="{{ asset('asset/mainan/toy1.jpg') }}" alt="Mainan Kucing">
          </div>
          <div class="slide-text">
            <h3>Mainan Anjing Interaktif</h3>
            <p>Mainan anjing agar tetap aktif dan tidak bosan.</p>
            <div class="price">Rp 9.000</div>
            <a href="#">
              <button>Beli Sekarang →</button>
            </a>
          </div>
        </div>

        <div class="swiper-slide">
          <div class="slide-img">
            <img src="{{ asset('asset/obat/adex.jpg') }}" alt="Shampoo Anjing">
          </div>
          <div class="slide-text">
            <h3>Obat Peliharaan</h3>
            <p>Suplemen & obat praktis untuk menjaga daya tahan dan vitalitas hewan kesayangan.</p>
            <div class="price">Rp 34.000</div>
            <a href="#">
              <button>Beli Sekarang →</button>
            </a>
          </div>
        </div>
      </div>
      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>
      <div class="swiper-pagination"></div>
    </div>
  </section>

  <!-- BESTSELLERS (Dynamic, leave as is) -->
  <section class="bestsellers">
    <h2>Produk yang mungkin Kamu suka</h2>
    <div class="product-grid" id="productGrid">
      @foreach($products as $product)
        @php
            $categorySlug = $product->category->slug ?? 'uncategorized';
            $productImage = !empty($product->gambar_produk) 
                ? Storage::url(is_array($product->gambar_produk) ? $product->gambar_produk[0] : $product->gambar_produk) 
                : asset('images/default-product.png');
        @endphp

        <div class="product-card" 
     data-name="{{ $product->nama_produk ?? 'Produk Tidak Diketahui' }}" 
     data-price="{{ $product->harga_produk ?? 0 }}" 
     data-popularity="{{ $product->popularity ?? 0 }}" 
     data-date="{{ strtotime($product->created_at ?? now()) }}"
     data-category="{{ $product->category->slug ?? 'uncategorized' }}">
     
    @php
        $promotion = $product->promotion ?? null;
        $originalPrice = $product->harga_produk ?? 0;
        $avgRating = $product->avg_rating ?? 0;
    @endphp

    {{-- Promotion badge --}}
    @if($promotion)
        <div class="promotion-badge">
            {{ $promotion->diskon }}% OFF
        </div>
    @endif

    <a href="{{ route('user.details', ['id' => $product->id]) }}" class="product-link">
        <img src="{{ !empty($product->gambar_produk) ? Storage::url(is_array($product->gambar_produk) ? $product->gambar_produk[0] : $product->gambar_produk) : asset('images/default-product.png') }}" 
             alt="{{ $product->nama_produk ?? 'Produk' }}">

        <div class="product-name">{{ $product->nama_produk ?? 'Produk Tidak Diketahui' }}</div>

        {{-- Price & Ratings flex --}}
        <div class="product-price-rating">
            <div class="product-price">
                @if($promotion)
                    <span class="original-price">Rp{{ number_format($originalPrice, 0, ',', '.') }}</span>
                @else
                    Rp{{ number_format($originalPrice, 0, ',', '.') }}
                @endif
            </div>

            <div class="product-rating">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= floor($avgRating))
                        <i class='bx bxs-star' style="color:#fbc02d"></i>
                    @elseif($i - $avgRating < 1)
                        <i class='bx bxs-star-half' style="color:#fbc02d"></i>
                    @else
                        <i class='bx bx-star' style="color:#fbc02d"></i>
                    @endif
                @endfor
                <span class="rating-value">({{ $avgRating }})</span>
            </div>
        </div>

        {{-- Promotion info --}}
        @if($promotion)
            @php
                $discountedPrice = $originalPrice * (1 - $promotion->diskon / 100);
            @endphp
            <div class="product-promotion">
                <span class="promotion-label">{{ $promotion->judul_promosi }}</span>
                <span class="promotion-price">Rp{{ number_format($discountedPrice, 0, ',', '.') }}</span>
                <p class="promotion-desc">{{ $promotion->deskripsi_promosi }}</p>
            </div>
        @endif
    </a>

    
</div>

      @endforeach
    </div>
  </section>

  
  <!-- FOOTER -->
  @include('partials.footer')

  <!-- Swiper.js -->
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script>
    const swiper = new Swiper('.swiper', {
      loop: true,
      navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
      pagination: { el: '.swiper-pagination', clickable: true },
    });

    // HITUNG MUNDUR (ke Minggu 23:59)
    // HITUNG MUNDUR (7 hari restart otomatis)
function startCountdown() {
  const countdown = document.getElementById("countdown");
  let deadline = getNext7Days();

  function getNext7Days() {
    const d = new Date();
    d.setDate(d.getDate() + 7); 
    d.setHours(23,59,59,999);
    return d;
  }

  function update() {
    const now = new Date();
    let diff = deadline - now;

    if (diff <= 0) {
      // Restart 7 hari lagi
      deadline = getNext7Days();
      diff = deadline - now;
    }

    const days = Math.floor(diff / (1000*60*60*24));
    const hours = Math.floor((diff / (1000*60*60)) % 24);
    const mins = Math.floor((diff / (1000*60)) % 60);
    const secs = Math.floor((diff / 1000) % 60);

    countdown.innerHTML = `
      <div class="time-box"><span>${days}</span><div class="label">Hari</div></div>
      <div class="time-box"><span>${hours}</span><div class="label">Jam</div></div>
      <div class="time-box"><span>${mins}</span><div class="label">Menit</div></div>
      <div class="time-box"><span>${secs}</span><div class="label">Detik</div></div>
    `;
  }

  update();
  setInterval(update, 1000);
}

startCountdown();


    // Notifikasi
    function showNotif(msg) {
      const notif = document.getElementById("notif");
      notif.textContent = msg;
      notif.classList.add("show");
      setTimeout(() => notif.classList.remove("show"), 2500);
    }

    // Tambah ke keranjang
    document.querySelectorAll('.add-cart').forEach(btn => {
      btn.addEventListener('click', function() {
        const card = this.closest('.product-card');
        const productId = card.getAttribute('data-id');

        // Kirim ke route Laravel cart
        fetch("{{ route('user.cart') }}", {
          method: "POST",
          headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Content-Type": "application/json"
          },
          body: JSON.stringify({ product_id: productId })
        })
        .then(res => res.json())
        .then(data => {
          showNotif("✅ Berhasil ditambahkan ke keranjang!");
        })
        .catch(() => {
          showNotif("❌ Gagal menambahkan!");
        });
      });
    });
  </script>

  <!-- Notification div -->
  <div id="notif"></div>

</body>
</html>
