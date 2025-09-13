let slides = document.querySelectorAll('.slide');
let currentSlide = 0;

function showNextSlide() {
    slides[currentSlide].classList.remove('active');
    currentSlide = (currentSlide + 1) % slides.length;
    slides[currentSlide].classList.add('active');
}

setInterval(showNextSlide, 3000); // change every 3 seconds

document.getElementById("search-btn").addEventListener("click", function() {
    let query = document.getElementById("search-input").value.trim();
    if(query) {
        alert("Mencari: " + query);
    } else {
        alert("Masukkan kata kunci pencarian!");
    }
});