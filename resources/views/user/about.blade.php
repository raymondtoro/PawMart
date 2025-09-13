<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PawMart</title>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
  <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user/about.css') }}">
</head>
<body>

  <!-- NAVBAR -->
  @include('partials.navbar')

  <!-- HERO -->
  <section class="hero">
    <div class="hero-text">
      <h1>Belanja mudah untuk<br>hewan peliharaan Anda<br><span>praktis dan terpercaya</span></h1>
<p>Produk pilihan dengan kualitas terbaik untuk mendukung kesehatan dan kebahagiaan hewan kesayangan.</p>

    </div>
    <div class="hero-img">
      <img src="{{ asset('asset/dogncat.png') }}" alt="Dog and Cat Transparent">
    </div>
  </section>

  <!-- REVIEWS -->
<h2 class="section-title">Apa Kata Mereka?</h2>
<div class="review-container">
    <div class="review-grid">
        @foreach($ratings as $rating)
            <div class="review-card">
                <p>"{{ $rating->ulasan }}"</p>
                <div class="review-info">
                    <span class="author">- {{ $rating->user->name }}</span>
                    <span class="product">({{ $rating->product->nama_produk }})</span>
                </div>
                <div class="stars-display">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $rating->bintang)
                            <i class='bx bxs-star' style="color:#fbc02d"></i>
                        @else
                            <i class='bx bx-star' style="color:#d3c3f7"></i>
                        @endif
                    @endfor
                </div>
            </div>
        @endforeach
    </div>

    <div class="review-btn">
        <a href="{{ route('user.rating') }}">
            <button>Berikan Review</button>
        </a>
    </div>
</div>

  </div>

  <!-- WHY CHOOSE US -->
  <h2 class="section-title">Mengapa Memilih Kami?</h2>
  <section class="why-choose">
    <div class="features">
      <div class="feature-box">
        <i class='bx bx-map'></i>
        <h3>Dibuat dengan Perhatian</h3>
        <p>Semua produk kami dipilih secara bertanggung jawab dan aman untuk hewan peliharaan Anda.</p>
      </div>
      <div class="feature-box">
        <i class='bx bx-dumbbell'></i>
        <h3>Kualitas Terjamin</h3>
        <p>Produk premium yang dirancang untuk menjaga kesehatan dan kebahagiaan hewan dalam jangka panjang.</p>
      </div>
      <div class="feature-box">
        <i class='bx bx-leaf'></i>
        <h3>Produk Alami</h3>
        <p>Alternatif ramah lingkungan dan aman untuk menjaga kesehatan hewan kesayangan Anda.</p>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  @include('partials.footer')

  <script>
    // Review slider logic
    const reviewGrid = document.getElementById("reviewGrid");
    const prevBtn = document.querySelector(".prev");
    const nextBtn = document.querySelector(".next");

    let offset = 0;

    nextBtn.addEventListener("click", () => {
      if (offset > -(reviewGrid.scrollWidth - reviewGrid.parentElement.offsetWidth)) {
        offset -= 270; // card width + gap
        reviewGrid.style.transform = `translateX(${offset}px)`;
      }
    });

    prevBtn.addEventListener("click", () => {
      if (offset < 0) {
        offset += 270;
        reviewGrid.style.transform = `translateX(${offset}px)`;
      }
    });
  </script>
</body>
</html>
