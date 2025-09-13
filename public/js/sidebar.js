const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    sidebarToggle.onclick = function() {
        sidebar.classList.toggle('open');
        sidebarOverlay.style.display = sidebar.classList.contains('open') ? 'block' : 'none';
    }
    sidebarOverlay.onclick = function() {
        sidebar.classList.remove('open');
        sidebarOverlay.style.display = 'none';
    }