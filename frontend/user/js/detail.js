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