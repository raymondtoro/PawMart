 <!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-toggle" id="sidebarToggle">
        <i class='bx bx-menu'></i>
    </div>
    <nav class="sidebar-nav">
        <a href="{{ route('admin.dashboard') }}" 
           class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}">
            <i class='bx bx-notepad'></i><span>Dashboard</span>
        </a>
        <a href="{{ route('admin.products.index') }}" 
            class="nav-link {{ Route::is('admin.products.*') ? 'active' : '' }}">
                <i class='bx bx-package'></i><span>Produk</span>
            </a>
        <a href="{{ route('admin.order') }}" 
           class="nav-link {{ Route::is('admin.order') ? 'active' : '' }}">
            <i class='bx bx-cart'></i><span>Order</span>
        </a>
        <a href="{{ route('admin.category') }}" 
           class="nav-link {{ Route::is('admin.category') ? 'active' : '' }}">
            <i class='bx bx-category'></i><span>Kategori</span>
        </a>
        <a href="{{ route('admin.promotion') }}" 
           class="nav-link {{ Route::is('admin.promotion') ? 'active' : '' }}">
            <i class='bx bx-file'></i><span>Promosi</span>
        </a>
    </nav>
    <div class="sidebar-bottom">
    <!-- Hidden logout form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- Logout trigger -->
    <a href="#" class="nav-link" onclick="openLogoutModal(event)">
        <i class='bx bx-log-out'></i><span>Logout</span>
    </a>
</div>
<!-- Logout Confirmation Modal -->
<div id="logoutModal" class="modal">
  <div class="modal-content slide-up">
    <h3>Konfirmasi Logout</h3>
    <p>Apakah Anda yakin ingin keluar dari akun?</p>
    <div class="modal-actions">
      <button class="btn cancel" onclick="closeLogoutModal()">Batal</button>
      <button class="btn confirm" onclick="confirmLogout()">Ya, Logout</button>
    </div>
  </div>
</div>



</aside>
<script>
function openLogoutModal(e) {
  e.preventDefault();
  document.getElementById("logoutModal").style.display = "flex";
}

function closeLogoutModal() {
  document.getElementById("logoutModal").style.display = "none";
}

function confirmLogout() {
  document.getElementById("logout-form").submit();
}
</script>


<!-- Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<!-- End Sidebar -->
