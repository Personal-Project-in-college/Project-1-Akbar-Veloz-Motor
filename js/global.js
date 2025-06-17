function showToast(message) {
  const existingToast = document.querySelector(".toast-notification");
  if (existingToast) {
    existingToast.remove();
  }

  const toast = document.createElement("div");
  toast.className = "toast-notification";
  toast.textContent = message;
  document.body.appendChild(toast);

  setTimeout(() => {
    toast.classList.add("show");
    setTimeout(() => {
      toast.remove();
    }, 3000);
  }, 100);
}

const whatsapp = () => {
  const contactNumber = 6282317264621;
  const message = "Hallo Akbar Veloz Motor, saya ingin menanyakan terkait kendaraan";
  const waUrl = `https://wa.me/${contactNumber}?text=${encodeURIComponent(message)}`;
  window.open(waUrl, "_blank");
};

window.addEventListener("storage", function (event) {
  if (event.key === "wishlist") {
    updateWishlistUI();
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const hamburgerMenu = document.getElementById("hamburgerMenu");
  const navLinks = document.getElementById("navLinks");
  const menuOverlay = document.getElementById("menuOverlay");
  // const testDriveLink = document.getElementById("testDriveLink");
  const logoutLink = document.getElementById("logoutLink");
  const isLoggedIn = logoutLink !== null;

  function toggleMenu() {
    hamburgerMenu.classList.toggle("active");
    navLinks.classList.toggle("show");
    menuOverlay.classList.toggle("show");
    document.body.classList.toggle("no-scroll");
  }

  if (hamburgerMenu) {
    hamburgerMenu.addEventListener("click", toggleMenu);
  }

  if (menuOverlay) {
    menuOverlay.addEventListener("click", toggleMenu);
  }

  // if (testDriveLink) {
  //   testDriveLink.addEventListener("click", function (event) {
  //     if (!isLoggedIn) {
  //       event.preventDefault();
  //       alert("Anda harus login terlebih dahulu untuk melakukan test drive.");
  //       window.location.href = "login.php";
  //     }
  //   });
  // }

  if (logoutLink) {
    logoutLink.addEventListener("click", async (e) => {
      e.preventDefault();
      try {
        const response = await fetch("./api/auth.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ action: "logout" }),
        });
        const data = await response.json();
        if (data.success) {
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

  const allNavLinks = document.querySelectorAll("#navLinks a");
  const currentPathname = window.location.pathname;

  allNavLinks.forEach((link) => {
    const linkPathname = new URL(link.href).pathname;
    const isHomePage = linkPathname.endsWith("/index.php") || linkPathname === "/";
    const onHomePage = currentPathname.endsWith("/index.php") || currentPathname === "/";

    if (linkPathname === currentPathname || (isHomePage && onHomePage)) {
      const isLoginLink = link.href.includes("login.php");

      if (link.id !== "logoutLink" && !isLoginLink) {
        link.classList.add("active");
        const svg = link.querySelector("svg");
        if (svg) {
          svg.classList.add("active");
        }
      }
    }
  });
});

