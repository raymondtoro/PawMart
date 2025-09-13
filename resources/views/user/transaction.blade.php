<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>PawMart - Checkout</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Fonts & Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

  <!-- Styles -->
  <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
  <link rel="stylesheet" href="{{ asset('css/user/transaction.css') }}">
</head>
<body>
  @include('partials.navbar')

  <main class="checkout-wrap">

    {{-- ================= Pesanan ================= --}}
    <section class="card order-card">
      <header class="card-header">
        <h2>Pesanan</h2>
        <span class="badge">{{ $orders->count() }} Barang</span>
      </header>

      <div class="order-list">
        @foreach ($orders as $o)
          @if ($o->product)
            @php
  $images = is_array($o->product->gambar_produk)
      ? $o->product->gambar_produk
      : json_decode($o->product->gambar_produk, true) ?? [];
  $mainImage = !empty($images)
      ? Storage::url($images[0])
      : asset('images/default-product.png');

  $promo = $o->product->promotion; // fetch promotion
  $diskon = $promo->diskon ?? 0; // percentage discount
  $harga = $o->product->harga_produk;
  $hargaFinal = $diskon > 0 ? round($harga * (1 - $diskon / 100)) : $harga;
@endphp


            <article class="order-item">
              <img src="{{ $mainImage }}" alt="{{ $o->product->nama_produk }}" class="order-img">
              <div class="order-info">
                <div class="order-name">{{ $o->product->nama_produk }}</div>
                <div class="order-qty">Jumlah: {{ $o->quantity }}</div>
              </div>
              <div class="order-price">Rp{{ number_format($hargaFinal * $o->quantity, 0, ',', '.') }}</div>
            </article>
          @endif
        @endforeach
      </div>
    </section>

    {{-- ================= Rincian Pembayaran ================= --}}
    <section class="card pay-card">
      <h2>Rincian Pembayaran</h2>

      <div class="method-preview" id="methodPreview">
        <div class="method-icon"><i class='bx bx-wallet'></i></div>
        <div class="method-text">
          <div class="method-title">Metode belum dipilih</div>
          <div class="method-sub">Silakan pilih metode pembayaran</div>
        </div>
        <button id="chooseMethodBtn" class="btn btn-ghost">Pilih Metode</button>
      </div>

      <label class="agree">
        <input type="checkbox" id="agreeTerms">
        <span>Saya setuju dengan syarat & ketentuan PawMart.</span>
      </label>

      <button id="primaryActionBtn" class="btn btn-primary btn-lg"
        data-total="Rp{{ number_format($total,0,',','.') }}">
        Pilih Metode
      </button>
    </section>

    {{-- ================= Ringkasan ================= --}}
<section class="order-summary">
  <h2>Ringkasan</h2>

  @php
    $totalDiskon = 0;
    $subtotalProduk = 0;

    foreach ($orders as $o) {
        if ($o->product) {
            $qty = $o->quantity;
            $harga = $o->product->harga_produk;
            $diskon = $o->product->promotion->diskon ?? 0;

            $hargaFinal = $diskon > 0 ? round($harga * (1 - $diskon / 100)) : $harga;
            $subtotalProduk += $harga * $qty;
            $totalDiskon += ($harga * $diskon / 100) * $qty;
        }
    }

    $totalWithShipping = $subtotalProduk - $totalDiskon + $shipping;
  @endphp

  <div class="sum-row">
    <span>Subtotal</span>
    <span>Rp{{ number_format($subtotalProduk, 0, ',', '.') }}</span>
  </div>
  <div class="sum-row text-success">
    <span>Diskon</span>
    <span>-Rp{{ number_format($totalDiskon, 0, ',', '.') }}</span>
  </div>
  <div class="sum-row">
    <span>Pengiriman</span>
    <span>Rp{{ number_format($shipping, 0, ',', '.') }}</span>
  </div>
  <div class="sum-total">
    <span>Total</span>
    <span class="sum-total-amount">Rp{{ number_format($totalWithShipping, 0, ',', '.') }}</span>
  </div>
</section>


  {{-- ================= Modal: Pilih Metode ================= --}}
  <div class="modal" id="methodModal" aria-hidden="true">
    <div class="modal-backdrop" data-close></div>
    <div class="modal-panel">
      <div class="modal-header">
        <h3>Pilih Metode Pembayaran</h3>
        <button class="icon-btn" data-close><i class='bx bx-x'></i></button>
      </div>

      <div class="method-groups">
        {{-- E-Wallet --}}
        <div class="group">
          <div class="group-title"><i class='bx bx-mobile'></i> Dompet Digital</div>
          <label class="method-option">
            <input type="radio" name="payMethod" value="ewallet:DANA">
            <div class="option-icon"><img src="{{ asset('asset/dana.jpg') }}" alt="Dana"></div>
            <div class="option-text"><div>DANA</div><small>Bayar via aplikasi DANA</small></div>
          </label>

          <label class="method-option">
            <input type="radio" name="payMethod" value="ewallet:OVO">
            <div class="option-icon"><img src="{{ asset('asset/ovo1.png') }}" alt="OVO"></div>
            <div class="option-text"><div>OVO</div><small>Bayar via aplikasi OVO</small></div>
          </label>
        </div>

        {{-- Transfer Bank --}}
        <div class="group">
          <div class="group-title"><i class='bx bx-bank'></i> Transfer Bank (VA)</div>
          <label class="method-option">
            <input type="radio" name="payMethod" value="bank:BCA">
            <div class="option-icon"><img src="{{ asset('asset/bca.png') }}" alt="BCA"></div>
            <div class="option-text"><div>Bank BCA</div><small>VA otomatis terverifikasi</small></div>
          </label>

          <label class="method-option">
            <input type="radio" name="payMethod" value="bank:BRI">
            <div class="option-icon"><img src="{{ asset('asset/bri.png') }}" alt="BRI"></div>
            <div class="option-text"><div>Bank BRI</div><small>VA otomatis terverifikasi</small></div>
          </label>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-ghost" data-close>Batal</button>
        <button class="btn btn-primary" id="confirmMethodBtn">Pilih Metode</button>
      </div>
    </div>
  </div>

  {{-- ================= Modal: Finalize Transaction ================= --}}
  <div class="modal" id="codeModal" aria-hidden="true">
    <div class="modal-backdrop" data-close></div>
    <div class="modal-panel panel-sm">
      <div class="modal-header">
        <h3 id="codeTitle">Kode Pembayaran</h3>
        <button class="icon-btn" data-close><i class='bx bx-x'></i></button>
      </div>

      <div class="code-box">
        <div class="code-meta" id="codeMeta">Metode</div>
        <div class="code-value" id="codeValue">—</div>
        <button class="btn btn-ghost" id="copyCodeBtn"><i class='bx bx-copy'></i> Salin</button>
      </div>

      <div class="code-help" id="codeHelp">
        Ikuti instruksi pada aplikasi bank atau e-wallet Anda untuk menyelesaikan pembayaran.
      </div>

      <div class="modal-footer">
        @php
  // Calculate totals with promotions
  $totalDiskon = 0;
  $subtotalProduk = 0;

  foreach ($orders as $o) {
      if ($o->product) {
          $qty = $o->quantity;
          $harga = $o->product->harga_produk;
          $diskon = $o->product->promotion->diskon ?? 0;

          $hargaFinal = $diskon > 0 ? round($harga * (1 - $diskon / 100)) : $harga;
          $subtotalProduk += $harga * $qty;
          $totalDiskon += ($harga * $diskon / 100) * $qty;
      }
  }

  $totalWithShipping = $subtotalProduk - $totalDiskon + $shipping;
@endphp

<form id="finalizeForm" action="{{ route('user.transaction.finalize') }}" method="POST">
  @csrf
  <input type="hidden" name="products" value='{{ $selectedJson }}'>
  <input type="hidden" name="quantities" value='{{ $qtyJson }}'>
  <input type="hidden" name="alamat_pengiriman" value="Alamat default">
  <input type="hidden" name="catatan" value="">
  <input type="hidden" name="metode_transaksi" id="metodeTransaksi">
  <input type="hidden" name="ongkir" value="{{ $shipping }}">
  <input type="hidden" name="subtotal" value="{{ $subtotalProduk }}">
  <input type="hidden" name="diskon" value="{{ $totalDiskon }}">
  <input type="hidden" name="total" value="{{ $totalWithShipping }}">

  <button type="submit" class="btn btn-primary" id="finishBtn">
    <i class='bx bx-check-circle'></i> Lihat Detail
  </button>
</form>

      </div>
    </div>
  </div>

  {{-- ================= Modal: Error ================= --}}
  <div class="modal" id="errorModal" aria-hidden="true">
    <div class="modal-backdrop" data-close></div>
    <div class="modal-panel panel-sm">
      <div class="modal-header">
        <h3>Peringatan</h3>
        <button class="icon-btn" data-close><i class='bx bx-x'></i></button>
      </div>
      <div class="error-content">
        <i class='bx bx-error-circle text-red-500' style="font-size:40px;margin-bottom:10px;"></i>
        <p id="errorMessage">Mohon centang persetujuan syarat & ketentuan.</p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" data-close>Tutup</button>
      </div>
    </div>
  </div>

  <script>
    window.PAWMART = {
      finalizeUrl: "{{ route('user.transaction.finalize') }}",
      csrf: "{{ csrf_token() }}",
      totalText: "Rp{{ number_format($total,0,',','.') }}"
    };
  </script>
  <script src="{{ asset('js/payment.js') }}"></script>
</body>
</html>
