<div class="top-navbar">
    <!-- Page Title -->
    <h2>{{ ucfirst(basename(request()->path())) ?: 'Dashboard' }}</h2>

    <div class="user-info">
        <!-- Notifications -->
        <div class="notification-wrapper">
            <span class="icon-bell" id="notifIcon">
                <i class='bx bx-bell'></i>
                <span class="badge" id="notifBadge" style="display:none;">0</span>
            </span>

            <!-- Dropdown -->
            <div class="notification-dropdown" id="notifDropdown">
                <h4>Notifikasi</h4>
                <ul id="notifList">
                    <li id="noNotif">Tidak ada notifikasi</li>
                </ul>
            </div>
        </div>

        <!-- Messages -->
        <a href="{{ route('admin.chat.index') }}" class="icon-message">
            <i class='bx bx-message-dots'></i>
            <span class="badge" id="chatBadge" style="display:none;">0</span>
        </a>

        <!-- Profile -->
        <a href="{{ route('admin.adminprofile') }}" class="user-profile">
            <img src="{{ Auth::user()->avatar 
                      ? asset('storage/' . Auth::user()->avatar) 
                      : asset('asset/default-avatar.png') }}" 
                 class="user-avatar" alt="User Avatar">
            <div class="user-details">
                <div class="username">{{ Auth::user()->name ?? 'Admin' }}</div>
                <div class="role">Admin</div>
            </div>
        </a>
    </div>
</div>

<!-- JS -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    const notifIcon = document.getElementById("notifIcon");
    const notifDropdown = document.getElementById("notifDropdown");
    const notifBadge = document.getElementById("notifBadge");
    const notifList = document.getElementById("notifList");
    const noNotif = document.getElementById("noNotif");
    const chatBadge = document.getElementById("chatBadge");

    let currentNotifications = [];

    // Toggle dropdown
notifIcon.addEventListener("click", async () => {
    notifDropdown.classList.toggle("show");

    // If opening dropdown, clear the badge & mark as read
    if (notifDropdown.classList.contains("show")) {
        notifBadge.style.display = "none";

        try {
            await fetch("{{ route('admin.adminnavbar.notifications.markAsRead') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ read: true }),
            });
        } catch (err) {
            console.error("Failed to mark notifications as read:", err);
        }
    }
});


    // Format "time ago" function
function timeAgo(isoDate) {
    const date = new Date(isoDate); // ISO8601 is safe for Date()
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);

    if (seconds < 60) return "baru saja";
const minutes = Math.floor(seconds / 60);
if (minutes < 60) return `${minutes} menit yang lalu`;
const hours = Math.floor(minutes / 60);
if (hours < 24) return `${hours} jam yang lalu`;
const days = Math.floor(hours / 24);
return `${days} hari yang lalu`;

}

// Render notifications in dropdown
function renderNotifications() {
    notifList.innerHTML = "";
    if (currentNotifications.length > 0) {
        currentNotifications.forEach(n => {
            const li = document.createElement("li");
            li.innerHTML = `${n.user} membeli "${n.produk}" <br>
                            <small class="text-muted">${timeAgo(n.created_at)}</small>`;
            notifList.appendChild(li);
        });
        
        noNotif.style.display = "none";
    } else {
        notifBadge.style.display = "none";
        notifList.appendChild(noNotif);
        noNotif.style.display = "block";
    }
}


    // Fetch notifications from backend
    async function fetchNotifications() {
    try {
        const res = await fetch("{{ route('admin.adminnavbar.notifications') }}");
        const data = await res.json();
        currentNotifications = data.notifications || [];

        renderNotifications();

        // Chat badge still works
        if (data.unreadMessages > 0) {
            chatBadge.textContent = data.unreadMessages;
            chatBadge.style.display = "inline-block";
        } else {
            chatBadge.style.display = "none";
        }
    } catch (err) {
        console.error("Failed to fetch notifications:", err);
    }
}


    // Update "time ago" every minute
    setInterval(renderNotifications, 60000);

    // Initial fetch + poll every 30s
    fetchNotifications();
    setInterval(fetchNotifications, 30000);
});
</script>


<!-- CSS -->
<style>
.top-navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    background: #fff;
    border-bottom: 1px solid #ddd;
    position: sticky;
    top: 0;
    z-index: 999;
}

.user-info {
    display: flex;
    align-items: center;
}

.notification-wrapper {
    position: relative;
    display: inline-block;
    margin-right: 15px;
}

.icon-bell, .icon-message {
    position: relative;
    cursor: pointer;
    font-size: 1.5rem;
    margin: 0 10px;
}

.badge {
    position: absolute;
    top: -5px;
    right: -8px;
    background: red;
    color: white;
    font-size: 11px;
    font-weight: bold;
    padding: 2px 6px;
    border-radius: 50%;
    line-height: 1;
}

.notification-dropdown {
    display: none;
    position: absolute;
    right: 0;
    top: 40px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    width: 280px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    z-index: 100;
    padding: 10px;
}

.notification-dropdown.show {
    display: block;
}

.notification-dropdown h4 {
    margin: 0 0 8px 0;
    font-size: 14px;
    font-weight: bold;
}

.notification-dropdown ul {
    list-style: none;
    padding: 0;
    margin: 0;
    max-height: 250px;
    overflow-y: auto;
}

.notification-dropdown ul li {
    padding: 6px 0;
    border-bottom: 1px solid #eee;
    font-size: 13px;
}

.notification-dropdown ul li:last-child {
    border-bottom: none;
}

.user-profile {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: inherit;
}

.user-avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    margin-right: 8px;
}

.text-muted {
    color: #888;
    font-size: 11px;
}
</style>
