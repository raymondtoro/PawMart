<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>PawMart - Shop</title>
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/user/shop.css') }}">
<link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
</head>
<body>

@include('partials.navbar')

<section class="hero">
  <div class="hero-overlay">
    <div class="hero-text">
      <h1>Temukan Produk Terbaik untuk Sahabat Berbulu Anda</h1>
      <p>Makanan, mainan, vitamin, dan aksesori berkualitas tinggi untuk semua jenis hewan peliharaan</p>
    </div>
  </div>
</section>

<div class="container">
  <aside class="sidebar">
    <h3>Kategori</h3>
    <ul class="category-list">
      @foreach($categories as $category)
        <li data-category="{{ strtolower($category->nama_kategori) }}"
    class="{{ $activeCategory == $category->id ? 'active' : '' }}">
    {{ $category->nama_kategori }} <i class='bx bx-chevron-right'></i>
</li>

      @endforeach
    </ul>
  </aside>

  <section class="products">
    <div class="products-header">
      <h2>Produk Kami</h2>
      <select class="sort" id="sortProducts">
        <option value="popularity">Populer</option>
        <option value="newest">Terbaru</option>
        <option value="price-asc">Harga: Rendah ke Tinggi</option>
        <option value="price-desc">Harga: Tinggi ke Rendah</option>
      </select>
    </div>

    @if(isset($message))
    <div class="alert alert-info" style="margin-bottom:15px; padding:10px; border:1px solid #d1d5db; border-radius:6px; background:#f3f4f6;">
        {!! $message !!}
    </div>
@endif


    <div class="product-grid" id="productGrid">
      @foreach($products as $product)
        @php
            $categoryName = strtolower($product->category->nama_kategori ?? 'uncategorized');
            $productImage = !empty($product->gambar_produk) 
                ? Storage::url($product->gambar_produk[0]) 
                : asset('images/default-product.png');
        @endphp

        <div class="product-card" 
     data-name="{{ $product->nama_produk ?? 'Produk Tidak Diketahui' }}" 
     data-price="{{ $product->harga_produk ?? 0 }}" 
     data-popularity="{{ $product->popularity ?? 0 }}" 
     data-date="{{ strtotime($product->created_at ?? now()) }}"
     data-category="{{ $categoryName }}">

    @php
        $promotion = $product->promotion ?? null;
        $originalPrice = $product->harga_produk ?? 0;
        $ratings = $product->ratings ?? collect(); 
        $avgRating = $ratings->count() ? round($ratings->avg('bintang'), 1) : 0;
    @endphp

    {{-- Promotion badge --}}
    @if($promotion)
        <div class="promotion-badge">
            {{ $promotion->diskon }}% OFF
        </div>
    @endif

    <a href="{{ route('user.details', ['id' => $product->id]) }}" class="product-link">
        <img src="{{ $productImage }}" alt="{{ $product->nama_produk ?? 'Produk' }}">
        <div class="product-name">{{ $product->nama_produk ?? 'Produk Tidak Diketahui' }}</div>

        <div class="product-price-rating">
    <div class="product-price">
        @if($promotion)
            <span class="original-price">Rp{{ number_format($originalPrice, 0, ',', '.') }}</span>
        @else
            Rp{{ number_format($originalPrice, 0, ',', '.') }}
        @endif
    </div>

    {{-- Ratings beside price --}}
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


        {{-- Promotion Info --}}
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

    {{-- Add to Cart button for authenticated users --}}
    @auth
        <button class="add-cart" 
            data-id="{{ $product->id }}" 
            data-name="{{ $product->nama_produk }}" 
            data-price="{{ $product->harga_produk ?? 0 }}" 
            data-image="{{ $productImage }}">
            <i class='bx bx-cart-add'></i>
        </button>
    @endauth

</div>


      @endforeach
    </div>
  </section>
</div>

<script src="{{ asset('js/shop.js') }}"></script>
</body>
</html>
