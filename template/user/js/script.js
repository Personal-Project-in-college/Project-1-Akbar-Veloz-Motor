// index.html
// Navbar toggle
function toggleMenu() {
  const navList = document.getElementById("navList");
  navList.classList.toggle("show");
}

// Carousel
document.addEventListener("DOMContentLoaded", function () {
  const slides = document.querySelector(".banner-slides");
  const slideItems = document.querySelectorAll(".banner-slide");
  const prevBtn = document.querySelector(".banner-prev");
  const nextBtn = document.querySelector(".banner-next");
  const indicators = document.querySelectorAll(".banner-indicator");

  let currentSlide = 0;
  const totalSlides = slideItems.length;
  let slideInterval;

  function goToSlide(index) {
    if (index < 0) index = totalSlides - 1;
    else if (index >= totalSlides) index = 0;

    slides.style.transform = `translateX(-${index * 100}%)`;
    currentSlide = index;

    indicators.forEach((indicator, i) => {
      indicator.classList.toggle("active", i === currentSlide);
    });
  }

  function nextSlide() {
    goToSlide(currentSlide + 1);
  }

  function prevSlide() {
    goToSlide(currentSlide - 1);
  }

  function startSlideShow() {
    slideInterval = setInterval(nextSlide, 5000);
  }

  function stopSlideShow() {
    clearInterval(slideInterval);
  }

  nextBtn.addEventListener("click", () => {
    nextSlide();
    stopSlideShow();
    startSlideShow();
  });

  prevBtn.addEventListener("click", () => {
    prevSlide();
    stopSlideShow();
    startSlideShow();
  });

  indicators.forEach((indicator) => {
    indicator.addEventListener("click", function () {
      const slideIndex = parseInt(this.getAttribute("data-slide"));
      goToSlide(slideIndex);
      stopSlideShow();
      startSlideShow();
    });
  });

  document.querySelector(".banner-container").addEventListener("mouseenter", stopSlideShow);
  document.querySelector(".banner-container").addEventListener("mouseleave", startSlideShow);

  startSlideShow();
});



// Simpan ke wishlist
function saveToWishlist(button) {
  const card = button.closest(".vehicle-card");
  const vehicle = {
    name: button.dataset.name || card.querySelector("h3")?.textContent,
    price: button.dataset.price || card.querySelector("p")?.textContent,
    image: button.dataset.image || card.querySelector("img")?.src,
    detailUrl: card.querySelector("a.btn-secondary")?.href || "detail.html",
  };

  let wishlist = JSON.parse(localStorage.getItem("wishlist")) || [];
  const existingIndex = wishlist.findIndex((item) => item.name === vehicle.name);

  if (existingIndex === -1) {
    wishlist.push(vehicle);
    button.innerHTML = '  <svg width="64px" height="64px" viewBox="0 -0.5 25 25" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M12.4997 18.9911L9.5767 15.9911L6.6767 12.9911C5.10777 11.3331 5.10777 8.73809 6.6767 7.08009C7.44494 6.34175 8.48548 5.95591 9.54937 6.01489C10.6133 6.07387 11.6048 6.57236 12.2867 7.39109L12.4997 7.60009L12.7107 7.38209C13.3926 6.56336 14.3841 6.06487 15.448 6.00589C16.5119 5.94691 17.5525 6.33275 18.3207 7.07109C19.8896 8.72909 19.8896 11.3241 18.3207 12.9821L15.4207 15.9821L12.4997 18.9911Z" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>';
    button.classList.add("saved");
    showToast(`${vehicle.name} ditambahkan ke wishlist`);
  } else {
    wishlist.splice(existingIndex, 1);
    button.innerHTML = '  <svg width="64px" height="64px" viewBox="0 -0.5 25 25" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M12.4997 18.9911L9.5767 15.9911L6.6767 12.9911C5.10777 11.3331 5.10777 8.73809 6.6767 7.08009C7.44494 6.34175 8.48548 5.95591 9.54937 6.01489C10.6133 6.07387 11.6048 6.57236 12.2867 7.39109L12.4997 7.60009L12.7107 7.38209C13.3926 6.56336 14.3841 6.06487 15.448 6.00589C16.5119 5.94691 17.5525 6.33275 18.3207 7.07109C19.8896 8.72909 19.8896 11.3241 18.3207 12.9821L15.4207 15.9821L12.4997 18.9911Z" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>';
    button.classList.remove("saved");
    showToast(`${vehicle.name} dihapus dari wishlist`);
  }

  localStorage.setItem("wishlist", JSON.stringify(wishlist));
}

// Update tombol wishlist sesuai localStorage
function updateWishlistButtons() {
  const wishlist = JSON.parse(localStorage.getItem("wishlist")) || [];
  document.querySelectorAll(".save-btn").forEach(button => {
    const vehicleName = button.dataset.name || button.closest(".vehicle-card").querySelector("h3")?.textContent;
    const isSaved = wishlist.some(item => item.name === vehicleName);
    button.innerHTML = isSaved ? '     <svg width="64px" height="64px" viewBox="0 -0.5 25 25" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M12.4997 18.9911L9.5767 15.9911L6.6767 12.9911C5.10777 11.3331 5.10777 8.73809 6.6767 7.08009C7.44494 6.34175 8.48548 5.95591 9.54937 6.01489C10.6133 6.07387 11.6048 6.57236 12.2867 7.39109L12.4997 7.60009L12.7107 7.38209C13.3926 6.56336 14.3841 6.06487 15.448 6.00589C16.5119 5.94691 17.5525 6.33275 18.3207 7.07109C19.8896 8.72909 19.8896 11.3241 18.3207 12.9821L15.4207 15.9821L12.4997 18.9911Z" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg> ' : '  <svg width="64px" height="64px" viewBox="0 -0.5 25 25" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M12.4997 18.9911L9.5767 15.9911L6.6767 12.9911C5.10777 11.3331 5.10777 8.73809 6.6767 7.08009C7.44494 6.34175 8.48548 5.95591 9.54937 6.01489C10.6133 6.07387 11.6048 6.57236 12.2867 7.39109L12.4997 7.60009L12.7107 7.38209C13.3926 6.56336 14.3841 6.06487 15.448 6.00589C16.5119 5.94691 17.5525 6.33275 18.3207 7.07109C19.8896 8.72909 19.8896 11.3241 18.3207 12.9821L15.4207 15.9821L12.4997 18.9911Z" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>';
    button.classList.toggle("saved", isSaved);
  });
}

// Inisialisasi tombol wishlist saat halaman dimuat
document.addEventListener("DOMContentLoaded", updateWishlistButtons);

// Data kendaraan (bisa diganti dari PHP)
const vehicles = [
  {
    image: "../assets/images/card/motor-beat.jpg",
    alt: "BeAT",
    name: "BeAT",
    price: "Rp. 18,880,000",
    detailUrl: "detail.html",
    type: "motor",
  },
  {
    image: "https://ik.imagekit.io/zlt25mb52fx/ahmcdn/tr:w-550,f-auto/uploads/product/thumbnail/thumbnail-product-beat-street-03062024-044327.png",
    alt: "BeAT Street",
    name: "BeAT Street",
    price: "Rp. 19,751,000",
    detailUrl: "detail.html",
    type: "motor",
  },
  {
    image: "https://ik.imagekit.io/zlt25mb52fx/ahmcdn/tr:w-550,f-auto/uploads/product/thumbnail/product-thumbnail-400x300px-red-metallic-06122024-030208.png",
    alt: "PCX 160",
    name: "PCX 160",
    price: "Rp. 34,300,000",
    detailUrl: "detail.html",
    type: "motor",
  },
  {
    image: "https://ik.imagekit.io/zlt25mb52fx/ahmcdn/tr:w-550,f-auto/uploads/product/thumbnail/thumbnail-new-supra-x-5-04032022-102907.png",
    alt: "Supra X 125 FI",
    name: "Supra X 125 FI",
    price: "Rp. 20,750,000",
    detailUrl: "detail.html",
    type: "motor",
  },
  {
    image: "https://www.hondaibrm.co.id/uploaded/7ec80b72756deac5f97b411020ae7605.webp",
    alt: "Honda Brio",
    name: "Honda Brio",
    price: "Rp. 184,000,000",
    detailUrl: "detail.html",
    type: "mobil",
  },
  {
    image: "https://www.hondaibrm.co.id/uploaded/b3ac95de9ac80348ee2fd99136df17d4.webp",
    alt: "Honda BR-V",
    name: "Honda BR-V",
    price: "Rp. 311,900,000",
    detailUrl: "detail.html",
    type: "mobil",
  },
];

// Render kendaraan
const semuaContainer = document.getElementById("semua-container");
const motorContainer = document.getElementById("motor-container");
const mobilContainer = document.getElementById("mobil-container");

function createCard(vehicle) {
  return `
    <div class="vehicle-card">
      <div class="card-image-wrapper" style="position: relative;">
        <img src="${vehicle.image}" alt="${vehicle.alt}">
        <button class="save-btn" onclick="saveToWishlist(this)"
                data-name="${vehicle.name}" 
                data-price="${vehicle.price}" 
                data-image="${vehicle.image}">
            <svg width="64px" height="64px" viewBox="0 -0.5 25 25" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M12.4997 18.9911L9.5767 15.9911L6.6767 12.9911C5.10777 11.3331 5.10777 8.73809 6.6767 7.08009C7.44494 6.34175 8.48548 5.95591 9.54937 6.01489C10.6133 6.07387 11.6048 6.57236 12.2867 7.39109L12.4997 7.60009L12.7107 7.38209C13.3926 6.56336 14.3841 6.06487 15.448 6.00589C16.5119 5.94691 17.5525 6.33275 18.3207 7.07109C19.8896 8.72909 19.8896 11.3241 18.3207 12.9821L15.4207 15.9821L12.4997 18.9911Z" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
        </button>
      </div>
      <div class="card-content">
        <h3>${vehicle.name}</h3>
        <p>Mulai dari ${vehicle.price}</p>
        <a href="${vehicle.detailUrl}" class="btn-secondary">Detail</a>
      </div>
    </div>`;
}

vehicles.forEach((vehicle) => {
  semuaContainer.innerHTML += createCard(vehicle);
  if (vehicle.type === "motor") motorContainer.innerHTML += createCard(vehicle);
  if (vehicle.type === "mobil") mobilContainer.innerHTML += createCard(vehicle);
});

// Sinkron ulang tombol wishlist setelah render
updateWishlistButtons();

// Fungsi untuk tab
function openTab(evt, tabName) {
  const tabcontent = document.querySelectorAll(".tabcontent");
  const tablinks = document.querySelectorAll(".tablinks");

  tabcontent.forEach((content) => (content.style.display = "none"));
  tablinks.forEach((link) => link.classList.remove("active"));

  document.getElementById(tabName).style.display = "block";
  evt.currentTarget.classList.add("active");

  updateWishlistButtons(); // <- tambahkan ini untuk update status tombol
}



// Sinkronisasi antar tab
window.addEventListener("storage", function (event) {
  if (event.key === "wishlist") {
    updateWishlistButtons();
  }
});

// Aktifkan tab default saat halaman pertama kali dibuka
document.querySelector(".tablinks.active")?.click();


