<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>PawMart - Detail Transaksi</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Fonts & Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

  <!-- Styles -->
  <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
  <link rel="stylesheet" href="{{ asset('css/user/show.css') }}">
</head>
<body>
  {{-- Navbar --}}
  @include('partials.navbar')

  <div class="transaction-container">
      <!-- Status Section -->
      <div class="status-section">
          <div class="status-icon success"><i class='bx bx-check'></i></div>
          <h2>Pembayaran Berhasil</h2>
          <p>Transaksi Anda telah berhasil diselesaikan.</p>
      </div>

      <!-- Transaction Card -->
      <div class="transaction-card">
          <div class="transaction-info">
              <div class="info-row">
                  <span>ID Transaksi</span>
                  <strong>{{ $transaction->id }}</strong>
              </div>
              <div class="info-row">
                  <span>ID Invoice</span>
                  <strong>INV-{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}</strong>
              </div>
              <div class="info-row">
                  <span>Tanggal</span>
                  <strong>{{ $transaction->tanggal_transaksi }}</strong>
              </div>
              <div class="info-row">
                  <span>Metode Pembayaran</span>
                  <strong>{{ $transaction->metode_transaksi }}</strong>
              </div>
              <div class="info-row">
                  <span>Alamat Pengiriman</span>
                  <strong>{{ $transaction->order->alamat_pengiriman ?? $transaction->order->user->alamat }}</strong>

              </div>

              @php
                  $subtotal = $transaction->order->orderItems->sum(fn($item) => $item->harga_saat_beli * $item->jumlah);
                  $ongkir = $transaction->ongkir ?? 0;
                  $total = $subtotal + $ongkir;
              @endphp

              <div class="info-row">
                  <span>Subtotal Produk</span>
                  <strong>Rp{{ number_format($subtotal, 0, ',', '.') }}</strong>
              </div>
              <div class="info-row">
                  <span>Ongkir</span>
                  <strong>Rp{{ number_format($ongkir, 0, ',', '.') }}</strong>
              </div>
              <div class="info-row">
                  <span>Total</span>
                  <strong>Rp{{ number_format($total, 0, ',', '.') }}</strong>
              </div>
              <div class="info-row">
                  <span>Status Pesanan</span>
                  <strong>{{ ucfirst($transaction->order->status_pesanan) }}</strong>
              </div>
          </div>

          <!-- Product List -->
          <div class="product-list">
              <h4>Produk yang Dibeli</h4>
              @foreach($transaction->order->orderItems as $item)
                  <div class="product-item">
                      <div class="product-thumb">
                          @if($item->produk && $item->produk->gambar_produk)
                              <img src="{{ asset('storage/' . $item->produk->gambar_produk[0]) }}" 
                                   alt="{{ $item->produk->nama_produk }}">
                          @else
                              <div class="no-image">Tidak Ada Gambar</div>
                          @endif
                      </div>
                      <div class="product-details">
                          <p class="product-name">{{ $item->produk->nama_produk ?? 'Produk sudah dihapus' }}</p>
                          <p class="product-qty">Jumlah: {{ $item->jumlah }}</p>
                          <p class="product-price">
                              Rp{{ number_format($item->harga_saat_beli, 0, ',', '.') }} x {{ $item->jumlah }} 
                              = <strong>Rp{{ number_format($item->harga_saat_beli * $item->jumlah, 0, ',', '.') }}</strong>
                          </p>
                      </div>
                  </div>
              @endforeach
          </div>
      </div>

      <!-- Buttons -->
      <div class="transaction-actions">
          <a href="{{ route('user.cart') }}" class="btn-secondary">Kembali ke Keranjang</a>
          <a href="{{ route('user.transaction.invoice', $transaction->id) }}" class="btn-primary">Unduh Invoice</a>
      </div>
  </div>
</body>
</html>
