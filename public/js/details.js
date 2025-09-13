// ========== TABS ==========
const tabs = document.querySelectorAll(".tab");
const contents = document.querySelectorAll(".tab-content");

tabs.forEach(tab => {
  tab.addEventListener("click", () => {
    tabs.forEach(t => t.classList.remove("active"));
    contents.forEach(c => c.classList.remove("active"));

    tab.classList.add("active");
    const target = tab.dataset.tab;
    document.getElementById(target).classList.add("active");
  });
});

// ========== MAIN IMAGE & THUMBNAILS ==========
const mainImage = document.getElementById("mainImage");
const thumbnails = document.querySelectorAll(".thumbnail-images img");

thumbnails.forEach(img => {
  img.addEventListener("click", () => {
    mainImage.src = img.dataset.img;
    thumbnails.forEach(t => t.classList.remove("active"));
    img.classList.add("active");
  });
});

// ========== FLAVOUR BUTTONS ==========
const flavourBtns = document.querySelectorAll(".flavour-buttons button");

flavourBtns.forEach(btn => {
  btn.addEventListener("click", () => {
    flavourBtns.forEach(b => b.classList.remove("active"));
    btn.classList.add("active");
    if (btn.dataset.img) {
      mainImage.src = btn.dataset.img;
    }
  });
});

// ========== REVIEWS (Reply toggle) ==========
function initReviewSection(container) {
  container.querySelectorAll(".toggle-replies").forEach(toggle => {
    toggle.addEventListener("click", (e) => {
      e.preventDefault();
      const replies = toggle.closest(".review").querySelector(".replies");
      replies.classList.toggle("hidden");
      toggle.textContent = replies.classList.contains("hidden") ? "Show Replies" : "Hide Replies";
    });
  });

  container.querySelectorAll(".add-reply .send-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      const container = btn.closest(".add-reply");
      const textarea = container.querySelector("textarea");
      const replyText = textarea.value.trim();
      if (!replyText) return;

      const repliesContainer = btn.closest(".review").querySelector(".replies");

      const reply = document.createElement("div");
      reply.className = "reply";
      reply.innerHTML = `
        <img src="https://via.placeholder.com/32" class="avatar" alt="user">
        <div>
          <strong>You</strong> <span class="time">just now</span>
          <p>${replyText}</p>
        </div>
      `;

      repliesContainer.appendChild(reply);
      repliesContainer.classList.remove("hidden");
      textarea.value = "";
    });
  });
}

// Initialize reviews only inside the reviews tab
const reviewsTab = document.getElementById("reviewsTab");
initReviewSection(reviewsTab);
