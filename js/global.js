const vehicles = [
  {
    id: 1,
    image:
      "https://img.lacakharga.com/public/images/2024/06/honda-beat-fi-tahun-2012-1718341847.jpg",
    alt: "BeAT",
    name: "BeAT",
    price: 18000000,
    minPrice: 16000000,
    detailUrl: "detail.php",
    type: "motor",
    description: "Motor matic ekonomis dengan desain sporty",
  },

  {
    id: 2,
    image:
      "https://ik.imagekit.io/zlt25mb52fx/ahmcdn/tr:w-550,f-auto/uploads/product/thumbnail/thumbnail-product-beat-street-03062024-044327.png",
    alt: "BeAT Street",
    name: "BeAT Street",
    price: 18000000,
    minPrice: 16000000,
    detailUrl: "detail.php",
    type: "motor",
    description: "Motor matic ekonomis dengan desain sporty",
  },
  {
    id: 3,
    image:
      "https://ik.imagekit.io/zlt25mb52fx/ahmcdn/tr:w-550,f-auto/uploads/product/thumbnail/product-thumbnail-400x300px-red-metallic-06122024-030208.png",
    alt: "PCX 160",
    name: "PCX 160",
    price: 18000000,
    minPrice: 16000000,
    detailUrl: "detail.php",
    type: "motor",
    description: "Motor matic ekonomis dengan desain sporty",
  },
  {
    id: 4,
    image:
      "https://ik.imagekit.io/zlt25mb52fx/ahmcdn/tr:w-550,f-auto/uploads/product/thumbnail/thumbnail-new-supra-x-5-04032022-102907.png",
    alt: "Supra X 125 FI",
    name: "Supra X 125 FI",
    price: 18000000,
    minPrice: 16000000,
    detailUrl: "detail.php",
    type: "motor",
    description: "Motor matic ekonomis dengan desain sporty",
  },
  {
    id: 5,
    image:
      "https://www.hondaibrm.co.id/uploaded/7ec80b72756deac5f97b411020ae7605.webp",
    alt: "Honda Brio",
    name: "Honda Brio",
    price: 18000000,
    minPrice: 16000000,
    detailUrl: "detail.php",
    type: "mobil",
    description: "Motor matic ekonomis dengan desain sporty",
  },
  {
    id: 6,
    image:
      "https://www.hondaibrm.co.id/uploaded/b3ac95de9ac80348ee2fd99136df17d4.webp",
    alt: "Honda BR-V",
    name: "Honda BR-V",
    price: 18000000,
    minPrice: 16000000,
    detailUrl: "detail.php",
    type: "mobil",
    description: "Motor matic ekonomis dengan desain sporty",
  },
];

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
document.addEventListener("DOMContentLoaded", function () {
  const hamburgerMenu = document.getElementById("hamburgerMenu");
  const navLinks = document.getElementById("navLinks");
  const menuOverlay = document.getElementById("menuOverlay");

  function toggleMenu() {
    hamburgerMenu.classList.toggle("active");
    
    // Toggle kelas 'show' untuk menampilkan/menyembunyikan navigasi
    navLinks.classList.toggle("show");
    
    // Toggle kelas 'show' untuk menampilkan/menyembunyikan overlay
    menuOverlay.classList.toggle("show");

    // Toggle kelas 'no-scroll' pada body untuk mencegah scrolling
    document.body.classList.toggle("no-scroll");
  }

  // Event listener untuk klik pada hamburger menu
  if (hamburgerMenu) {
    hamburgerMenu.addEventListener("click", toggleMenu);
  }

  // Event listener untuk klik pada overlay (untuk menutup menu)
  if (menuOverlay) {
    menuOverlay.addEventListener("click", toggleMenu);
  }
});

// Navbar aktif
// This part needs to be adjusted to ensure correct class application
const allNavLinks = document.querySelectorAll("nav ul li a");

allNavLinks.forEach((link) => {
  // Check if the link's href matches the current page's pathname
  // Ensure the link's href is relative and matches the pathname without domain
  const linkPathname = new URL(link.href).pathname;
  const currentPathname = window.location.pathname;

  console.log('link-path ' ,linkPathname);
  console.log('current-path',currentPathname);
  

  if (linkPathname === currentPathname || (linkPathname === "/index.php" && (currentPathname === "/" || currentPathname === "/index.php"))) {
    // Add 'active' class to the link itself
    link.classList.add("active");

    // If there's an SVG within the link, add 'active' class to it too
    const svg = link.querySelector("svg");
    if (svg) {
      svg.classList.add("active");
    }
  }
});

// Call Us (no changes needed)
const callUs = () => {
  const contactNumber = 6282317264621;
  const message = `Hallo Akbar Veloz Motor, saya ingin menanyakan terkait kendaraan`;
  const waUrl = `https://wa.me/${contactNumber}?text=${encodeURIComponent(message)}`; // Use encodeURIComponent
  window.open(waUrl, "_blank");
};

// tombol login berubah menjadi logout
const logoutLink = document.getElementById("logoutLink");

if (logoutLink) {
  logoutLink.addEventListener("click", async (e) => {
    e.preventDefault(); // Mencegah navigasi default link

    try {
      const response = await fetch("./api/auth.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({
          action: "logout"
        }),
      });
      const data = await response.json();
      if (data.success) {
        // Arahkan ke halaman utama setelah logout
        window.location.href = "index.php";
      } else {
        alert("Gagal logout: " + data.message);
      }
    } catch (error) {
      console.error("Kesalahan saat logout pelanggan:", error);
      alert("Terjadi kesalahan saat logout.");
    }
  });
}

// path login jangan aktive