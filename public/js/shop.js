document.addEventListener("DOMContentLoaded", () => {
    const productGrid = document.getElementById("productGrid");
    const sortSelect = document.getElementById("sortProducts");
    const categoryItems = document.querySelectorAll(".category-list li");
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

    // Default active category from first visible product (after search or load)
const firstCard = productGrid.querySelector(".product-card");
let activeCategory = firstCard ? firstCard.dataset.category.toLowerCase().trim() : null;

// Highlight sidebar category based on activeCategory
categoryItems.forEach(li => {
    li.classList.remove("active");
    if (activeCategory && li.dataset.category.toLowerCase().trim() === activeCategory) {
        li.classList.add("active");
    }
});


    /** ========== 🛒 Update Quantity Badges ========== */
    async function updateQtyBadges() {
        try {
            const response = await fetch("/user/cart", {
                headers: { "X-Requested-With": "XMLHttpRequest" }
            });

            // Make sure CartController@index returns JSON if ajax
            const data = await response.json();

            document.querySelectorAll(".product-card").forEach(card => {
                const productId = card.querySelector(".add-cart")?.dataset.id;
                if (!productId) return;

                const qty = data[productId] || 0;
                let badge = card.querySelector(".qty-badge");

                if (qty > 0) {
                    if (!badge) {
                        badge = document.createElement("div");
                        badge.className = "qty-badge";
                        badge.style.cssText =
                            "position:absolute;top:5px;right:5px;background:red;color:#fff;border-radius:50%;padding:3px 7px;font-size:12px;font-weight:bold;";
                        card.appendChild(badge);
                    }
                    badge.textContent = qty;
                } else if (badge) {
                    badge.remove();
                }
            });
        } catch (err) {
            console.error("❌ Gagal mengambil keranjang:", err);
        }
    }

    /** ========== 🛒 Add to Cart (AJAX) ========== */
    productGrid.addEventListener("click", async e => {
        const btn = e.target.closest(".add-cart");
        if (!btn) return;

        const productId = btn.dataset.id;
        const productName = btn.dataset.name;

        try {
            const response = await fetch("/user/cart/add", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: JSON.stringify({ produk_id: productId })
            });

            const data = await response.json();

            // ✅ Show notification
            showToast(data.message || `${productName} berhasil ditambahkan ke keranjang 🛒`, btn);

            // ✅ Refresh cart badges
            updateQtyBadges();
        } catch (err) {
            console.error("❌ Gagal tambah produk:", err);
            alert("Terjadi kesalahan saat menambahkan produk ke keranjang.");
        }
    });

    /** ========== 🔔 Toast Notification ========== */
    function showToast(message, btn) {
        const notif = document.createElement("div");
        notif.className = "cart-notif";
        notif.textContent = message;
        notif.style.cssText =
            "position:absolute;bottom:60px;right:10px;background:#aa46b9;color:#fff;padding:8px 12px;border-radius:5px;font-size:14px;opacity:0;transition:opacity 0.25s;";

        btn.parentElement.appendChild(notif);

        requestAnimationFrame(() => (notif.style.opacity = 1));
        setTimeout(() => {
            notif.style.opacity = 0;
            setTimeout(() => notif.remove(), 250);
        }, 1500);
    }

    /** ========== 📂 Category Filter ========== */
    categoryItems.forEach(li => {
        li.addEventListener("click", () => {
            activeCategory = li.dataset.category.toLowerCase().trim();
            categoryItems.forEach(item => item.classList.remove("active"));
            li.classList.add("active");
            filterAndSort();
        });
    });

    /** ========== ↕️ Sorting ========== */
    sortSelect.addEventListener("change", filterAndSort);

    function filterAndSort() {
        const cards = Array.from(productGrid.children);

        // Filter by category
        const visibleCards = cards.filter(
            card => card.dataset.category.toLowerCase().trim() === activeCategory
        );

        // Hide all cards
        cards.forEach(card => (card.style.display = "none"));

        // Sort visible cards
        const option = sortSelect.value;
        visibleCards.sort((a, b) => {
            switch (option) {
                case "price-asc":
                    return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                case "price-desc":
                    return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                case "newest":
                    return parseInt(b.dataset.date) - parseInt(a.dataset.date);
                case "popularity":
                    return parseInt(b.dataset.popularity) - parseInt(a.dataset.popularity);
                default:
                    return 0;
            }
        });

        // Show with animation
        visibleCards.forEach((card, index) => {
            card.style.display = "block";
            card.style.opacity = 0;
            card.style.transform = "translateY(20px)";
            productGrid.appendChild(card);
            setTimeout(() => {
                card.style.transition = "opacity 0.4s ease, transform 0.4s ease";
                card.style.opacity = 1;
                card.style.transform = "translateY(0)";
            }, index * 50);
        });
    }

    // Init
    filterAndSort();
    updateQtyBadges();
});
