// Fungsi untuk menampilkan notifikasi toast
function showToast(message) {
  // Hapus toast sebelumnya jika ada
  const existingToast = document.querySelector(".toast-notification");
  if (existingToast) {
    existingToast.remove();
  }

  // Buat toast baru
  const toast = document.createElement("div");
  toast.className = "toast-notification";
  toast.textContent = message;
  document.body.appendChild(toast);

  // Tampilkan toast
  setTimeout(() => {
    toast.classList.add("show");
    setTimeout(() => {
      toast.remove();
    }, 3000);
  }, 100);
}

window.addEventListener("storage", function (event) {
  if (event.key === "wishlist") {
    updateWishlistUI();
  }
});

// Navbar toggle
function toggleMenu() {
  const navList = document.getElementById("navList");
  navList.classList.toggle("show");
}

// Navbar aktif
const navLinks = document.querySelectorAll("nav ul li a");

navLinks.forEach((link) => {
  if (link.pathname === window.location.pathname) {
    const svg = link.querySelector("svg");
    if (svg) {
      svg.classList.add("active");
    }
    link.classList.add("active");
  }
});

// Call Us
const callUs = () => {
  contactNumber = 6282317264621;

  const message = `Hallo Akbar Veloz Motor, saya ingin menanyakan terkait kendaraan`;

  const waUrl = `https://wa.me/${contactNumber}?text=${message}`;
  window.open(waUrl, "_blank");
};


