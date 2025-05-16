// Navbar
// toggle
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

  // Fungsi untuk menggeser slide
  function goToSlide(index) {
    if (index < 0) {
      index = totalSlides - 1;
    } else if (index >= totalSlides) {
      index = 0;
    }

    slides.style.transform = `translateX(-${index * 100}%)`;
    currentSlide = index;

    // Update indikator
    indicators.forEach((indicator, i) => {
      if (i === currentSlide) {
        indicator.classList.add("active");
      } else {
        indicator.classList.remove("active");
      }
    });
  }

  // Fungsi untuk slide berikutnya
  function nextSlide() {
    goToSlide(currentSlide + 1);
  }

  // Fungsi untuk slide sebelumnya
  function prevSlide() {
    goToSlide(currentSlide - 1);
  }

  // Mulai slideshow otomatis
  function startSlideShow() {
    slideInterval = setInterval(nextSlide, 5000);
  }

  // Berhenti slideshow otomatis
  function stopSlideShow() {
    clearInterval(slideInterval);
  }

  // Event listeners
  nextBtn.addEventListener("click", function () {
    nextSlide();
    stopSlideShow();
    startSlideShow();
  });

  prevBtn.addEventListener("click", function () {
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

  // Hentikan slideshow saat hover
  document
    .querySelector(".banner-container")
    .addEventListener("mouseenter", stopSlideShow);
  document
    .querySelector(".banner-container")
    .addEventListener("mouseleave", startSlideShow);

  // Mulai slideshow saat halaman dimuat
  startSlideShow();
});

// fungsi tab
function openCity(evt, cityName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";
}