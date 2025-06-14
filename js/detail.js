// Update tombol wishlist sesuai localStorage
function updateWishlistButtons() {
  button = document.getElementById("simpan");

  // console.log(button);

  const vehicle = {
    name: document.getElementById("detail-title").textContent,
    price: document.getElementById("harga").textContent,
    image: document.getElementById("mainImage").src,
    detailUrl: window.location.href,
  };

  let wishlist = JSON.parse(localStorage.getItem("wishlist")) || [];
  const existingIndex = wishlist.findIndex(
    (item) => item.name === vehicle.name
  );

  if (existingIndex !== -1) {
    button.innerHTML = `<svg width="35px" height="35px" viewBox="0 -0.5 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                <g id="SVGRepo_iconCarrier">
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M12.4997 18.9911L9.5767 15.9911L6.6767 12.9911C5.10777 11.3331 5.10777 8.73809 6.6767 7.08009C7.44494 6.34175 8.48548 5.95591 9.54937 6.01489C10.6133 6.07387 11.6048 6.57236 12.2867 7.39109L12.4997 7.60009L12.7107 7.38209C13.3926 6.56336 14.3841 6.06487 15.448 6.00589C16.5119 5.94691 17.5525 6.33275 18.3207 7.07109C19.8896 8.72909 19.8896 11.3241 18.3207 12.9821L15.4207 15.9821L12.4997 18.9911Z" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                </g>
              </svg>
              Simpan`;
    button.classList.add("saved");
  }
}

// Inisialisasi tombol wishlist saat halaman dimuat
document.addEventListener("DOMContentLoaded", updateWishlistButtons);

// Zoom effect on image hover
const zoomContainer = document.querySelector(".zoom-container");
const zoomImage = zoomContainer.querySelector("img");

zoomContainer.addEventListener("mousemove", (e) => {
  const { left, top, width, height } = zoomContainer.getBoundingClientRect();
  const x = ((e.clientX - left) / width) * 100;
  const y = ((e.clientY - top) / height) * 100;
  zoomImage.style.transformOrigin = `${x}% ${y}%`;
  zoomImage.style.transform = "scale(2)";
});

zoomContainer.addEventListener("mouseleave", () => {
  zoomImage.style.transformOrigin = "center center";
  zoomImage.style.transform = "scale(1)";
});

document.addEventListener("DOMContentLoaded", () => {
  const mainImage = document.querySelector(".avm-main-image img");
  const thumbnails = document.querySelectorAll(".avm-thumbnail");
  const modal = document.getElementById("imageModal");
  const modalImg = document.getElementById("modalImage");

  const closeModalBtn = document.querySelector(".avm-close-modal");
  const prevBtn = document.querySelector(".avm-modal-prev");
  const nextBtn = document.querySelector(".avm-modal-next");
  const zoomInBtn = document.querySelector(".avm-zoom-in");
  const zoomOutBtn = document.querySelector(".avm-zoom-out");
  const resetZoomBtn = document.querySelector(".avm-reset-zoom");

  let currentIndex = 0;
  let zoomLevel = 1;

  const allImages = [mainImage.src, ...[...thumbnails].map((t) => t.src)];

  function openModal(index) {
    currentIndex = index;
    modal.style.display = "flex";
    modalImg.src = allImages[currentIndex];
    resetZoom();
    document.body.style.overflow = "hidden";
  }

  function closeModal() {
    modal.style.display = "none";
    document.body.style.overflow = "auto";
  }

  function showImage(index) {
    currentIndex = (index + allImages.length) % allImages.length;
    modalImg.src = allImages[currentIndex];
    resetZoom();
  }

  function zoom(delta, cursorX = null, cursorY = null) {
    zoomLevel = Math.min(3, Math.max(1, zoomLevel + delta));

    if (cursorX && cursorY) {
      const rect = modalImg.getBoundingClientRect();
      const offsetX = cursorX - rect.left;
      const offsetY = cursorY - rect.top;
      const originX = (offsetX / rect.width) * 100;
      const originY = (offsetY / rect.height) * 100;
      modalImg.style.transformOrigin = `${originX}% ${originY}%`;
    } else {
      modalImg.style.transformOrigin = "center center";
    }

    modalImg.style.transform = `scale(${zoomLevel})`;
    modalImg.style.cursor = zoomLevel > 1 ? "grab" : "zoom-in";
  }

  function resetZoom() {
    zoomLevel = 1;
    modalImg.style.transform = "scale(1)";
    modalImg.style.cursor = "zoom-in";
  }

  mainImage.addEventListener("click", () => openModal(0));
  thumbnails.forEach((thumb, i) => {
    thumb.addEventListener("click", (e) => {
      const index = parseInt(e.target.dataset.index);
      openModal(index);
    });
  });

  closeModalBtn.addEventListener("click", closeModal);
  prevBtn.addEventListener("click", () => showImage(currentIndex - 1));
  nextBtn.addEventListener("click", () => showImage(currentIndex + 1));
  zoomInBtn.addEventListener("click", () => zoom(0.5));
  zoomOutBtn.addEventListener("click", () => zoom(-0.5));
  resetZoomBtn.addEventListener("click", resetZoom);

  modal.addEventListener("click", (e) => {
    if (e.target === modal) closeModal();
  });

  modalImg.addEventListener("wheel", (e) => {
    e.preventDefault();
    zoom(e.deltaY < 0 ? 0.2 : -0.2, e.clientX, e.clientY);
  });

  // Drag to pan
  let isDragging = false,
    startX,
    startY;

  modalImg.addEventListener("mousedown", (e) => {
    if (zoomLevel <= 1) return;
    isDragging = true;
    startX = e.clientX;
    startY = e.clientY;
    modalImg.style.cursor = "grabbing";
  });

  modal.addEventListener("mousemove", (e) => {
    if (!isDragging) return;
    e.preventDefault();
    const dx = (e.clientX - startX) * 0.5;
    const dy = (e.clientY - startY) * 0.5;
    modal.scrollLeft -= dx;
    modal.scrollTop -= dy;
    startX = e.clientX;
    startY = e.clientY;
  });

  ["mouseup", "mouseleave"].forEach((event) =>
    modal.addEventListener(event, () => {
      if (zoomLevel > 1) modalImg.style.cursor = "grab";
      isDragging = false;
    })
  );

  // Keyboard shortcuts
  document.addEventListener("keydown", (e) => {
    if (modal.style.display !== "flex") return;
    switch (e.key) {
      case "Escape":
        closeModal();
        break;
      case "ArrowLeft":
        showImage(currentIndex - 1);
        break;
      case "ArrowRight":
        showImage(currentIndex + 1);
        break;
      case "+":
        zoom(0.2);
        break;
      case "-":
        zoom(-0.2);
        break;
      case "0":
        resetZoom();
        break;
    }
  });
});

// WA detail kendaraan
const waDetailKendaraan = () => {
  const detailTitle = document.getElementById("detail-title").textContent;
  contactNumber = 6282317264621;
  const message = `Hallo Akbar Veloz Motor, saya ingin menanyakan terkait kendaraan: ${detailTitle}`;

  const waUrl = `https://wa.me/${contactNumber}?text=${message}`;
  window.open(waUrl, "_blank");
};

// Simpan ke wishlist
function saveToWishlist(button) {
  const vehicle = {
    name: document.getElementById("detail-title").textContent,
    price: document.getElementById("harga").textContent,
    image: document.getElementById("mainImage").src,
    detailUrl: window.location.href,
  };

  let wishlist = JSON.parse(localStorage.getItem("wishlist")) || [];
  const existingIndex = wishlist.findIndex(
    (item) => item.name === vehicle.name
  );

  console.log(existingIndex);

  if (existingIndex === -1) {
    wishlist.push(vehicle);
    button.innerHTML = `<svg width="35px" height="35px" viewBox="0 -0.5 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                <g id="SVGRepo_iconCarrier">
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M12.4997 18.9911L9.5767 15.9911L6.6767 12.9911C5.10777 11.3331 5.10777 8.73809 6.6767 7.08009C7.44494 6.34175 8.48548 5.95591 9.54937 6.01489C10.6133 6.07387 11.6048 6.57236 12.2867 7.39109L12.4997 7.60009L12.7107 7.38209C13.3926 6.56336 14.3841 6.06487 15.448 6.00589C16.5119 5.94691 17.5525 6.33275 18.3207 7.07109C19.8896 8.72909 19.8896 11.3241 18.3207 12.9821L15.4207 15.9821L12.4997 18.9911Z" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                </g>
              </svg>
              Simpan`;
    button.classList.add("saved");
    showToast(`${vehicle.name} ditambahkan ke wishlist`);
  } else {
    wishlist.splice(existingIndex, 1);
    button.innerHTML = ` <svg width="35px" height="35px" viewBox="0 -0.5 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                <g id="SVGRepo_iconCarrier">
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M12.4997 18.9911L9.5767 15.9911L6.6767 12.9911C5.10777 11.3331 5.10777 8.73809 6.6767 7.08009C7.44494 6.34175 8.48548 5.95591 9.54937 6.01489C10.6133 6.07387 11.6048 6.57236 12.2867 7.39109L12.4997 7.60009L12.7107 7.38209C13.3926 6.56336 14.3841 6.06487 15.448 6.00589C16.5119 5.94691 17.5525 6.33275 18.3207 7.07109C19.8896 8.72909 19.8896 11.3241 18.3207 12.9821L15.4207 15.9821L12.4997 18.9911Z" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                </g>
              </svg>
              Simpan`;
    button.classList.remove("saved");
    showToast(`${vehicle.name} dihapus dari wishlist`);
  }

  localStorage.setItem("wishlist", JSON.stringify(wishlist));
}

// Fungsi untuk tab
function openTab(evt, tabName) {
  const tabcontent = document.querySelectorAll(".tabcontent");
  const tablinks = document.querySelectorAll(".tablinks");

  tabcontent.forEach((content) => (content.style.display = "none"));
  tablinks.forEach((link) => link.classList.remove("active"));

  document.getElementById(tabName).style.display = "block";
  evt.currentTarget.classList.add("active");
}

// Logika
document.addEventListener("DOMContentLoaded", () => {
  const buttons = document.querySelectorAll('.credit-form__period-button');
  const vehiclePriceInput = document.getElementById('vehicle-price');
  const dpInput = document.getElementById('down-payment');
  const interestRateInput = document.getElementById('interest-rate');
  const calculateButton = document.querySelector('.credit-form__action-button');
  const paymentValue = document.querySelector('.credit-form__payment-value');

  let selectedMonths = 0;

  buttons.forEach(button => {
    button.addEventListener('click', () => {
      buttons.forEach(btn => btn.classList.remove('active'));
      button.classList.add('active');
      selectedMonths = parseInt(button.textContent);
    });
  });

  calculateButton.addEventListener('click', () => {
    const rawPrice = vehiclePriceInput.value.replace(/[^\d]/g, '');
    const price = parseInt(rawPrice);
    const rawDP = dpInput.value.replace(/[^\d]/g, '');
    const dp = parseInt(rawDP);
    const interestRate = parseFloat(interestRateInput.value); // tahunan (%)

    if (!selectedMonths) {
      alert("Pilih periode cicilan terlebih dahulu.");
      return;
    }

    if (dp < 0.25 * price) {
      alert("Minimal DP adalah 25% dari harga kendaraan.");
      return;
    }

    const principal = price - dp;
    const monthlyInterest = interestRate / 12 / 100;
    const totalInterest = principal * monthlyInterest * selectedMonths;

    // const adminFee = 500000;
    const adminFee = 0;
    const totalPayment = principal + totalInterest + adminFee;
    const monthlyInstallment = totalPayment / selectedMonths;

    paymentValue.textContent = formatRupiah(monthlyInstallment);
  });

  function formatRupiah(angka) {
    return 'Rp. ' + angka.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  }
});