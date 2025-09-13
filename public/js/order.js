const sortSelect = document.getElementById("sortOrders");
const ordersList = document.getElementById("ordersList");

function parseDate(dateStr) {
    const [day, monthStr, year] = dateStr.split(" ");
    const months = {
        "Januari": 0, "Februari": 1, "Maret": 2, "April": 3, "Mei": 4, "Juni": 5,
        "Juli": 6, "Agustus": 7, "September": 8, "Oktober": 9, "November": 10, "Desember": 11
    };
    return new Date(year, months[monthStr], parseInt(day));
}

function sortOrders(order = "desc") {
    if (!ordersList) return;

    let rows = Array.from(ordersList.querySelectorAll(".order-row"));
    rows.sort((a, b) => {
        const dateA = parseDate(a.querySelector(".right").innerText.split("\n")[0]);
        const dateB = parseDate(b.querySelector(".right").innerText.split("\n")[0]);
        return order === "desc" ? dateB - dateA : dateA - dateB;
    });

    ordersList.innerHTML = "";
    rows.forEach(r => ordersList.appendChild(r));
}

if (sortSelect) {
    sortSelect.addEventListener("change", () => sortOrders(sortSelect.value));
    // default load terbaru
    sortOrders("desc");
}
