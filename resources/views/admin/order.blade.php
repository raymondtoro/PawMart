<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Pesanan | PawMart</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />

  <!-- Boxicons -->
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

  <!-- Sidebar CSS -->
  <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="{{ asset('css/admin/order.css') }}">

  <style>
    /* Success Modal */
    .success-modal,
    .error-modal {
      display: none;
      position: fixed;
      z-index: 10000;
      left: 0; top: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.4);
      justify-content: center;
      align-items: center;
      animation: fadeIn 0.3s ease;
    }
    .success-modal-content,
    .error-modal-content {
      background: #fff;
      padding: 30px 40px;
      border-radius: 15px;
      text-align: center;
      box-shadow: 0 6px 20px rgba(0,0,0,0.2);
      animation: scaleUp 0.3s ease;
    }
    .success-icon {
      font-size: 60px;
      color: #16a34a; /* green */
      margin-bottom: 15px;
    }
    .error-icon {
      font-size: 60px;
      color: #dc2626; /* red */
      margin-bottom: 15px;
    }
    .success-modal h3,
    .error-modal h3 {
      margin: 0;
      font-size: 1.2rem;
      color: #333;
      font-weight: 600;
    }
    @keyframes scaleUp {
      from { transform: scale(0.8); opacity: 0; }
      to { transform: scale(1); opacity: 1; }
    }
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }
  </style>
</head>
<body>
<div class="dashboard-wrapper">
  @include('partials.sidebar')

  <main class="main-content">
    <div class="top-navbar">
      <h2>Daftar Pesanan</h2>
    </div>

    <table class="clean-table">
      <thead>
        <tr>
          <th>No</th>
          <th>User</th>
          <th>Tanggal Pesanan</th>
          <th>Total Pesanan</th>
          <th>Status</th>
          <th>Alamat</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($orders as $index => $order)
        @php
          switch($order->status_pesanan) {
              case 'pending':  $statusClass = 'status-pending'; break;
              case 'diproses': $statusClass = 'status-diproses'; break;
              case 'dikirim':  $statusClass = 'status-dikirim'; break;
              case 'selesai':  $statusClass = 'status-selesai'; break;
              case 'batal':    $statusClass = 'status-batal'; break;
              default:         $statusClass = 'status-default'; break;
          }
        @endphp
        <tr data-order='@json($order->load("orderItems.produk"))'>
  <td>{{ $index + 1 }}</td>
  <td>{{ $order->user->name ?? $order->user_id }}</td>
  <td>{{ $order->created_at->format('d/m/Y') }}</td>
  <td>Rp {{ number_format($order->total_pesanan, 0, ',', '.') }}</td>
  <td><span class="status-badge {{ $statusClass }}">{{ ucfirst($order->status_pesanan) }}</span></td>
  <td>{{ $order->alamat_pengiriman }}</td>
  <td class="table-actions">
    <button class="icon-btn view-order-btn">
      <i class='bx bx-show-alt'></i>
    </button>
    <button class="icon-btn edit-status-btn" data-order-id="{{ $order->id }}" data-current-status="{{ $order->status_pesanan }}">
      <i class='bx bx-edit'></i>
    </button>
  </td>
</tr>

        @endforeach
      </tbody>
    </table>
<div class="pagination-wrapper" style="margin-top:20px; display:flex; justify-content:center; gap:10px; align-items:center;">
    {{-- Previous Page --}}
    @if ($orders->onFirstPage())
        <span class="page-btn disabled">&lt;</span>
    @else
        <a href="{{ $orders->previousPageUrl() }}" class="page-btn">&lt;</a>
    @endif

    {{-- Page Numbers --}}
    @foreach ($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
        @if ($page == $orders->currentPage())
            <span class="page-btn active">{{ $page }}</span>
        @else
            <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
        @endif
    @endforeach

    {{-- Next Page --}}
    @if ($orders->hasMorePages())
        <a href="{{ $orders->nextPageUrl() }}" class="page-btn">&gt;</a>
    @else
        <span class="page-btn disabled">&gt;</span>
    @endif
</div>


  </main>
</div>

<!-- Order Summary Modal -->
<div class="modal" id="orderModal">
  <div class="modal-content">
    <h3>Ringkasan Pesanan</h3>
    <div class="modal-body" id="orderDetails">
      <!-- Dynamic content will be injected here -->
    </div>
    <div class="btn-group" style="margin-top:20px; text-align:right;">
      <button class="btn-cancel" id="closeOrderModal">Tutup</button>
    </div>
  </div>
</div>

<!-- Change Status Modal -->
<div class="modal" id="statusModal">
  <div class="modal-content">
    <h3>Ubah Status Pesanan</h3>
    <form id="statusForm">
      <input type="hidden" name="order_id" id="statusOrderId">
      <label for="newStatus">Pilih Status:</label>
      <select name="status" id="newStatus" required>
        <option value="pending">Pending</option>
        <option value="diproses">Diproses</option>
        <option value="dikirim">Dikirim</option>
        <option value="selesai">Selesai</option>
        <option value="batal">Batal</option>
      </select>
      <div class="btn-group" style="margin-top:20px; text-align:right;">
        <button type="button" class="btn-cancel" id="closeStatusModal">Batal</button>
        <button type="submit" class="btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="success-modal">
  <div class="success-modal-content">
    <div class="success-icon">
      <i class='bx bx-check-circle'></i>
    </div>
    <h3>Status berhasil diubah!</h3>
  </div>
</div>

<!-- Error Modal -->
<div id="errorModal" class="error-modal">
  <div class="error-modal-content">
    <div class="error-icon">
      <i class='bx bx-x-circle'></i>
    </div>
    <h3>Gagal mengubah status!</h3>
  </div>
</div>

<script src="{{ asset('js/sidebar.js') }}"></script>
<script>
// ==========================
// order.js (kategori style)
// ==========================

// ===== Utility =====
function openModal(modalOrId) {
  const modal = typeof modalOrId === 'string' ? document.getElementById(modalOrId) : modalOrId;
  if (!modal) return;
  modal.style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

function closeModal(modalOrId) {
  const modal = typeof modalOrId === 'string' ? document.getElementById(modalOrId) : modalOrId;
  if (!modal) return;
  modal.style.display = 'none';
  document.body.style.overflow = '';
}

function closeAllModals() {
  document.querySelectorAll('.modal').forEach(m => m.style.display = 'none');
  document.body.style.overflow = '';
}

// ===== Modal Elements =====
const orderModal = document.getElementById('orderModal');
const orderDetails = document.getElementById('orderDetails');
const closeOrderBtn = document.getElementById('closeOrderModal');

const statusModal = document.getElementById('statusModal');
const closeStatusBtn = document.getElementById('closeStatusModal');
const statusForm = document.getElementById('statusForm');

const successModal = document.getElementById('successModal');
const errorModal = document.getElementById('errorModal');

// ===== Status Badge Classes =====
const statusColors = {
  pending: 'status-pending',
  diproses: 'status-diproses',
  dikirim: 'status-dikirim',
  selesai: 'status-selesai',
  batal: 'status-batal',
  default: 'status-default'
};

// ===== View Order Summary =====
document.querySelectorAll('.view-order-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    const row = btn.closest('tr');
    const order = JSON.parse(row.dataset.order);

    let html = `<p><strong>User:</strong> ${order.user?.name || order.user_id}</p>`;
    html += `<p><strong>Tanggal Pesanan:</strong> ${new Date(order.created_at).toLocaleDateString('id-ID')}</p>`;
    html += `<p><strong>Alamat:</strong> ${order.alamat_pengiriman}</p><hr><div class="order-items">`;

    order.order_items.forEach(item => {
      html += `<div style="display:flex;justify-content:space-between;margin-bottom:8px;">
        <span>${item.produk.nama_produk} x ${item.jumlah}</span>
        <span>Rp ${Number(item.harga_saat_beli * item.jumlah).toLocaleString('id-ID')}</span>
      </div>`;
    });

    html += '</div><hr>';
    html += `<p><strong>Total Pesanan:</strong> Rp ${Number(order.total_pesanan).toLocaleString('id-ID')}</p>`;

    orderDetails.innerHTML = html;
    openModal(orderModal);
  });
});

closeOrderBtn?.addEventListener('click', () => closeModal(orderModal));

// ===== Change Status Modal =====
document.querySelectorAll('.edit-status-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.getElementById('statusOrderId').value = btn.dataset.orderId;
    document.getElementById('newStatus').value = btn.dataset.currentStatus;
    openModal(statusModal);
  });
});

closeStatusBtn?.addEventListener('click', () => closeModal(statusModal));

// ===== Status Form Submit =====
statusForm?.addEventListener('submit', e => {
  e.preventDefault();
  const orderId = document.getElementById('statusOrderId').value;
  const newStatus = document.getElementById('newStatus').value;

  fetch(`/admin/order/${orderId}/status`, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ status: newStatus })
  })
  .then(res => res.json())
  .then(data => {
    closeModal(statusModal);

    if (data.success) {
      const row = document.querySelector(`.edit-status-btn[data-order-id="${orderId}"]`).closest('tr');
      const badge = row.querySelector('.status-badge');

      // Update badge class
      Object.values(statusColors).forEach(c => badge.classList.remove(c));
      badge.classList.add(statusColors[newStatus] || statusColors.default);
      badge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);

      showSuccess('Status berhasil diubah!');

      // Update dataset if summary modal open
      if (orderModal.style.display === 'flex') {
        const orderData = JSON.parse(row.dataset.order);
        orderData.status_pesanan = newStatus;
        row.dataset.order = JSON.stringify(orderData);
        row.querySelector('.view-order-btn')?.click();
      }
    } else {
      showError('Gagal mengubah status!');
    }
  })
  .catch(() => showError('Gagal mengubah status!'));
});

// ===== Success / Error =====
function showSuccess(msg = 'Berhasil!') {
  closeAllModals();
  successModal.querySelector('h3').textContent = msg;
  openModal(successModal);
  setTimeout(() => closeModal(successModal), 2000);
}

function showError(msg = 'Gagal!') {
  closeAllModals();
  errorModal.querySelector('h3').textContent = msg;
  openModal(errorModal);
  setTimeout(() => closeModal(errorModal), 2000);
}

// ===== Close Modals on Backdrop Click =====
window.addEventListener('click', e => {
  [orderModal, statusModal, successModal, errorModal].forEach(modal => {
    if (modal && e.target === modal) closeModal(modal);
  });
});
</script>


<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
</body>
</html>
