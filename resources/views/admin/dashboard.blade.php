<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | PetShop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Boxicons CDN -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin/adminboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/adminnavbar.css') }}">

</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        @include('partials.sidebar')

        <!-- Main Content -->
        <main class="main-content">
            @include('partials.adminnavbar')
            <div class="content">
                <div class="section-header">
                    <h3>Ringkasan</h3>
                    <div class="underline"></div>
                </div>
                <!-- Summary Cards -->
                <div class="summary-cards">
                    <div class="card card-white summary-card-row">
                        <div class="card-title">TOTAL ORDER</div>
                        <div class="card-value">{{ $totalOrders }}</div>
                    </div>
                    <div class="card card-white summary-card-row">
                        <div class="card-title">PRODUK HABIS</div>
                        <div class="card-value">{{ $outOfStock }}</div>
                    </div>
                    <div class="card card-white summary-card-row">
                        <div class="card-title">PENGHASILAN</div>
                        <div class="card-value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                    </div>
                    <div class="card card-white summary-card-row">
                        <div class="card-title">PENILAIAN</div>
                        <div class="card-value">{{ number_format($avgRating, 2, ',', '.') }}</div>
                    </div>
                </div>
                <!-- End Summary Cards -->

                <div class="main-flex-row">
                    <!-- Latest Orders -->
                <div class="latest-orders">
                    <div class="orders-header">
                        <h4>Riwayat Pesanan</h4>
                        <a href="{{ route('admin.order') }}" class="see-all">Lihat Semua <i class='bx bx-right-arrow-alt'></i></a>
                    </div>
                    <div class="orders-list">
                        @foreach($latestOrders as $order)
                            <div class="order-row">
                                <div>
                                    <span class="dot"></span>
                                    <span class="bold">{{ $order->user->name ?? 'Unknown' }}</span><br>
                                    <span class="desc">{{ $order->kode_pesanan }}</span>
                                </div>
                                <div class="order-date">
                                    {{ $order->created_at->translatedFormat('d F Y') }}<br>
                                    <span class="price">Rp{{ number_format($order->total_pesanan, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <!-- End Latest Orders -->


                    <!-- Inventory Report -->
                    <div class="inventory-report">
                        <div class="report-header">
                            <h4>Laporan persediaan</h4>
                        </div>
                        <div class="report-list">
                            @foreach($inventory as $item)
                            @php
                                $percentage = $maxStock > 0 ? ($item->total_stok / $maxStock) * 100 : 0;
                            @endphp
                            <div class="report-row">
                                <span>{{ $item->nama_kategori }}</span>
                                <div class="progress-bar">
                                    <div style="width: {{ $percentage }}%;"></div>
                                </div>
                                <span class="right">{{ $item->total_stok }}</span>
                            </div>
                        @endforeach

                        </div>
                    </div>
                    <!-- End Inventory Report -->
                </div>
            </div>
        </main>
        <!-- End Main Content -->
    </div>
    <script src="{{ asset('js/sidebar.js') }}"></script>

</body>
</html>
