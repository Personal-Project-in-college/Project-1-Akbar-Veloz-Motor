document.addEventListener("DOMContentLoaded", function() {
    initializeCarousel();
    updateWishlistButtons();
});

function toggleMenu() {
    const navList = document.getElementById("navList");
    navList.classList.toggle("show");
}

function openTab(evt, tabName) {
    const tabcontent = document.querySelectorAll(".tabcontent");
    const tablinks = document.querySelectorAll(".tablinks");

    tabcontent.forEach((content) => content.style.display = "none");
    tablinks.forEach((link) => link.classList.remove("active"));

    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.classList.add("active");
    updateWishlistButtons();
}

window.addEventListener("storage", function(event) {
    if (event.key === "wishlist") {
        updateWishlistButtons();
    }
});

function saveToWishlist(button) {
    const vehicle = {
        id: button.dataset.id,
        name: button.dataset.name,
        price: button.dataset.price,
        image: button.dataset.image,
        detailUrl: button.dataset.detailUrl,
    };

    let wishlist = JSON.parse(localStorage.getItem("wishlist")) || [];
    const existingIndex = wishlist.findIndex((item) => item.id === vehicle.id);

    if (existingIndex === -1) {
        wishlist.push(vehicle);
        showToast(`${vehicle.name} ditambahkan ke wishlist`);
    } else {
        wishlist.splice(existingIndex, 1);
        showToast(`${vehicle.name} dihapus dari wishlist`);
    }

    localStorage.setItem("wishlist", JSON.stringify(wishlist));
    updateWishlistButtons();
}

function updateWishlistButtons() {
    const wishlist = JSON.parse(localStorage.getItem("wishlist")) || [];
    document.querySelectorAll(".save-btn").forEach((button) => {
        const vehicleId = button.dataset.id;
        const isSaved = wishlist.some((item) => item.id === vehicleId);
        button.classList.toggle("saved", isSaved);
    });
}

function initializeCarousel() {
    const slides = document.querySelector(".banner-slides");
    if (!slides) return;

    const slideItems = document.querySelectorAll(".banner-slide");
    const prevBtn = document.querySelector(".banner-prev");
    const nextBtn = document.querySelector(".banner-next");
    const indicators = document.querySelectorAll(".banner-indicator");
    if (!prevBtn || !nextBtn || slideItems.length === 0) return;

    let currentSlide = 0;
    const totalSlides = slideItems.length;
    let slideInterval;

    function goToSlide(index) {
        currentSlide = (index + totalSlides) % totalSlides;
        slides.style.transform = `translateX(-${currentSlide * 100}%)`;
        indicators.forEach((indicator, i) => {
            indicator.classList.toggle("active", i === currentSlide);
        });
    }

    function startSlideShow() {
        stopSlideShow();
        slideInterval = setInterval(() => goToSlide(currentSlide + 1), 5000);
    }

    function stopSlideShow() {
        clearInterval(slideInterval);
    }

    nextBtn.addEventListener("click", () => {
        goToSlide(currentSlide + 1);
        startSlideShow();
    });

    prevBtn.addEventListener("click", () => {
        goToSlide(currentSlide - 1);
        startSlideShow();
    });

    indicators.forEach((indicator, index) => {
        indicator.addEventListener("click", () => {
            goToSlide(index);
            startSlideShow();
        });
    });

    document.querySelector(".banner-container")?.addEventListener("mouseenter", stopSlideShow);
    document.querySelector(".banner-container")?.addEventListener("mouseleave", startSlideShow);

    startSlideShow();
}

function showToast(message) {
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        document.body.appendChild(toastContainer);
    }

    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.textContent = message;
    toastContainer.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('show');
    }, 100);

    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            if (toast.parentElement) {
                toast.parentElement.removeChild(toast);
            }
        }, 500);
    }, 3000);
}