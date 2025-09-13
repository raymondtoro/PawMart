<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PawMart - Keranjang</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
  <link rel="stylesheet" href="{{ asset('css/user/cart.css') }}">
</head>
<body>
  @include('partials.navbar')

  <div class="cart-wrapper">
    <div class="cart-items">
      <h2>Keranjang Anda</h2>
      <p class="subtitle">Produk yang kamu pilih</p>
      <div class="items-list"></div>

      <div class="select-all-wrapper">
        <input type="checkbox" id="selectAll">
        <label for="selectAll">Pilih Semua</label>
      </div>

      <div class="remove-all">
        <button type="button" id="removeAllBtn">Hapus semua dari keranjang</button>
      </div>
    </div>

    <div class="cart-summary">
      <div class="summary-details">
        <div><span>Total Item:</span><span id="items-total">Rp0</span></div>
        <div><span>Biaya Pengiriman:</span><span id="delivery-cost">Rp0</span></div>
        <div><span>Diskon:</span><span id="discount">Rp0</span></div>
        <div class="total"><span>Total:</span><span id="grand-total">Rp0</span></div>
      </div>

      <form id="checkoutForm" action="{{ route('user.transaction.buy') }}" method="POST">
        @csrf
        <button type="submit" class="checkout">Bayar <i class='bx bx-right-arrow-alt'></i></button>
      </form>

      <div class="delivery">
        <i class='bx bxs-truck'></i>
        <span>Gratis ongkir di atas Rp100.000</span>
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


  <script src="{{ asset('js/cart.js') }}"></script>
</body>
</html>
