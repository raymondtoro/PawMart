<footer class="footer">
  <div class="footer-container">

    <!-- Left Column -->
    <div class="footer-col">
      <h4>PawMart</h4>
      <p>123 Pet Street, Jakarta, Indonesia</p>
      <p>+62 8990086304</p>
      <p>info@pawmart.co</p>
    </div>

    <!-- Center Column -->
    <div class="footer-col center">
      <div class="footer-logo">
        <img src="{{ asset('asset/logo123(1).png') }}" alt="PawMart Text Logo" class="text-animate">
      </div>
      <p class="footer-desc">Kebahagiaan hewan peliharaan Anda adalah prioritas kami.</p>
      <div class="footer-social">
        <a href="https://www.instagram.com/pawmart.co/"><i class='bx bxl-instagram'></i></a>
        <a href="https://x.com/_PawMart?t=GVFqhwxm170y4j1iXZnFBg&s=09" target="_blank" aria-label="X (Twitter)">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
        <path d="M18.244 2.25h3.308l-7.227 8.26 8.5 11.24H16.06l-5.155-6.74-5.902 6.74H1.695l7.66-8.73-8.21-10.77h7.91l4.67 6.16 5.52-6.16z"/>
      </svg>
    </a>
        <a href="https://wa.me/628990086304"><i class='bx bxl-whatsapp'></i></a>
      </div>
    </div>

    <!-- Right Column -->
    <div class="footer-col">
      <h4>Halaman</h4>
      <ul>
        <li><a href="{{ route('dashboard') }}">Beranda</a></li>
        <li><a href="{{ route('shop') }}">Produk</a></li>
        <li><a href="{{ route('about') }}">Tentang Kami</a></li>
        <li><a href="#">Kontak</a></li>
      </ul>
    
    </div>

  </div>

  <!-- Bottom Bar -->
  <div class="footer-bottom">
    © 2025 PawMart. All Rights Reserved.
  </div>
</footer>
