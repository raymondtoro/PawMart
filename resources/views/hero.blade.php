<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PawMart</title>
    <!-- Boxicons CDN -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/hero.css') }}">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
</head>
<body>

<!-- SPLASH SCREEN -->
<div class="splash">
    <div class="splash-content">
        <img src="{{ asset('asset/house(1).png') }}" alt="Cat Logo" class="logo-animate">
        <img src="{{ asset('asset/logo123(1).png') }}" alt="PawMart Text Logo" class="text-animate">
    </div>
    <div class="pulse"></div>
</div>

<!-- NAVBAR (included separately) -->
@include('partials.navbar')

<!-- SLIDESHOW HERO -->
<div class="slideshow-container">
    <div class="slide active" style="background-image: url('{{ asset('asset/ginger.jpg') }}');"></div>
    <div class="slide" style="background-image: url('{{ asset('asset/dog.jpg') }}');"></div>
    <div class="slide" style="background-image: url('{{ asset('asset/whitecat.jpg') }}');"></div>

    <div class="hero-text">
        <h1>Kami menyediakan<br>Produk yang luar biasa</h1>
    </div>
</div>

<!-- PRODUCTS SECTION -->
<section class="products">
    <h2>Produk unggulan</h2>
    <p>Jelajahi item terlaris kami</p>

    <a href="{{ route('shop') }}" class="btn">Jelajahi</a>

    <div class="product-grid">
        <a href="{{ route('user.details', ['id' => 9]) }}" class="product-card">
            <img src="{{ asset('asset/36.png') }}" alt="Makanan Kucing">
            <h3>Makanan kucing</h3>
            <p>Rp59.000</p>
        </a>
        <a href="{{ route('user.details', ['id' => 29]) }}" class="product-card">
            <img src="{{ asset('asset/foods/dog5.jpg') }}" alt="Makanan Anjing">
            <h3>Makanan Anjing</h3>
            <p>Rp69.000</p>
        </a>
        <a href="{{ route('user.details', ['id' => 8]) }}" class="product-card">
            <img src="{{ asset('asset/dogtoy.png') }}" alt="Mainan">
            <h3>Mainan Peliharaan</h3>
            <p>Rp23.000</p>
        </a>
        <a href="{{ route('user.details', ['id' => 10]) }}" class="product-card">
            <img src="{{ asset('asset/vit.jpg') }}" alt="Vitamin">
            <h3>Vitamin Peliharaan</h3>
            <p>Rp79.000</p>
        </a>
    </div>
</section>
<!-- Footer -->
  @include('partials.footer')

<script src="{{ asset('js/hero.js') }}"></script>
</body>
</html>
