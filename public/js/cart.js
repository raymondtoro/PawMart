document.addEventListener("DOMContentLoaded", () => {
    const itemsList = document.querySelector(".items-list");
    const itemsTotal = document.getElementById("items-total");
    const deliveryCost = document.getElementById("delivery-cost");
    const discount = document.getElementById("discount");
    const grandTotal = document.getElementById("grand-total");
    const removeAllBtn = document.getElementById("removeAllBtn");
    const checkoutForm = document.getElementById("checkoutForm");
    const selectAllCheckbox = document.getElementById("selectAll");

    let cartData = [];
    const formatRupiah = num => "Rp" + Number(num).toLocaleString("id-ID");

    /** Load cart from backend */
    const loadCart = () => {
        fetch("/user/cart/data")
        .then(res => res.json())
        .then(data => {
            cartData = data.map(item => ({ ...item, selected: true }));
            renderCart();
        });
    };

    /** Render cart items */
    const renderCart = () => {
        itemsList.innerHTML = "";

        if (!cartData.length) {
            itemsList.innerHTML = "<p>Keranjang kosong.</p>";
            updateTotals();
            return;
        }

        cartData.forEach(item => {
            const diskon = item.product.diskon ?? 0;
            const price = item.product.harga_produk;
            const discountedPrice = diskon > 0 ? Math.round(price * (1 - diskon / 100)) : price;

            const cartItem = document.createElement("div");
            cartItem.className = "cart-item";
            cartItem.dataset.id = item.cart_id;

            cartItem.innerHTML = `
                <input type="checkbox" class="item-checkbox" ${item.selected ? "checked" : ""}>
                <img src="/storage/${item.product.gambar_produk}" alt="${item.product.nama_produk}">
                <div class="item-details">
                    <h3>${item.product.nama_produk}</h3>
                    <div class="price-display">
                        ${diskon > 0
                            ? `<span class="original-price" style="text-decoration:line-through">${formatRupiah(price)}</span>
                               <span class="discounted-price">${formatRupiah(discountedPrice)}</span>
                               <span class="discount-badge">-${diskon}%</span>`
                            : `<span class="discounted-price">${formatRupiah(price)}</span>`}
                    </div>
                    <p>Harga per item: ${formatRupiah(discountedPrice)}</p>
                </div>
                <div class="item-actions">
                    <span class="price">${formatRupiah(discountedPrice * item.quantity)}</span>
                    <div class="quantity">
                        <button class="qty-btn decrease">-</button>
                        <input type="number" class="qty-input" min="1" value="${item.quantity}">
                        <button class="qty-btn increase">+</button>
                    </div>
                    <button class="remove-btn">×</button>
                </div>
            `;
            itemsList.appendChild(cartItem);
        });

        attachEvents();
        updateTotals();
        selectAllCheckbox.checked = cartData.every(i => i.selected);
    };

    /** Update totals */
    const updateTotals = () => {
        let subtotal = 0, delivery = 0;
        cartData.forEach(item => {
            if (!item.selected) return;
            const diskon = item.product.diskon ?? 0;
            const price = item.product.harga_produk;
            const discountedPrice = diskon > 0 ? Math.round(price * (1 - diskon / 100)) : price;
            subtotal += discountedPrice * item.quantity;
            delivery += 10000 * item.quantity;
        });
        const promoDiscount = subtotal > 200000 ? 20000 : 0;
        const grand = subtotal + delivery - promoDiscount;

        itemsTotal.textContent = formatRupiah(subtotal);
        deliveryCost.textContent = formatRupiah(delivery);
        discount.textContent = formatRupiah(promoDiscount);
        grandTotal.textContent = formatRupiah(grand);
    };

    /** Attach events */
    const attachEvents = () => {
        document.querySelectorAll(".qty-btn.decrease").forEach(btn => {
            btn.onclick = () => {
                const input = btn.nextElementSibling;
                const id = btn.closest(".cart-item").dataset.id;
                if (parseInt(input.value) > 1) {
                    input.value = parseInt(input.value) - 1;
                    updateQuantity(id, parseInt(input.value));
                }
            };
        });
        document.querySelectorAll(".qty-btn.increase").forEach(btn => {
            btn.onclick = () => {
                const input = btn.previousElementSibling;
                const id = btn.closest(".cart-item").dataset.id;
                input.value = parseInt(input.value) + 1;
                updateQuantity(id, parseInt(input.value));
            };
        });
        document.querySelectorAll(".qty-input").forEach(input => {
            input.onchange = () => {
                const id = input.closest(".cart-item").dataset.id;
                const val = Math.max(1, parseInt(input.value));
                input.value = val;
                updateQuantity(id, val);
            };
        });
        document.querySelectorAll(".remove-btn").forEach(btn => {
            btn.onclick = () => removeItem(btn.closest(".cart-item").dataset.id);
        });
        document.querySelectorAll(".item-checkbox").forEach(cb => {
            cb.onchange = () => {
                const id = cb.closest(".cart-item").dataset.id;
                const item = cartData.find(i => i.cart_id == id);
                if (item) item.selected = cb.checked;
                updateTotals();
                selectAllCheckbox.checked = cartData.every(i => i.selected);
            };
        });
    };

    /** Backend actions */
    const updateQuantity = (cartId, qty) => {
        fetch("/user/cart/update", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]").content
            },
            body: JSON.stringify({ cart_id: cartId, quantity: qty })
        }).then(() => loadCart());
    };
    const removeItem = cartId => {
        fetch("/user/cart/remove", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]").content
            },
            body: JSON.stringify({ cart_id: cartId })
        }).then(() => loadCart());
    };

    selectAllCheckbox.addEventListener("change", () => {
        cartData.forEach(i => i.selected = selectAllCheckbox.checked);
        renderCart();
    });
    removeAllBtn.addEventListener("click", () => {
        fetch("/user/cart/clear", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]").content
            }
        }).then(() => loadCart());
    });

    /** Checkout form submit */
    checkoutForm.addEventListener("submit", function(e){
        const selected = cartData.filter(i => i.selected);
        if(selected.length === 0){
            e.preventDefault();
            alert("Pilih produk terlebih dahulu");
            return;
        }

        document.querySelectorAll(".dynamic-cart-input").forEach(i => i.remove());

        selected.forEach(item => {
            const input = document.createElement("input");
            input.type = "hidden";
            input.name = "produk_id[]";
            input.value = item.cart_id;
            input.classList.add("dynamic-cart-input");
            this.appendChild(input);
        });
    });

    loadCart();
});
