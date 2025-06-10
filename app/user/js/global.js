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



//  fungsi chat widget
// DOM Elements
const chatButton = document.getElementById("chatButton");
const chatPanel = document.getElementById("chatPanel");
const chatHeader = document.getElementById("chatHeader");
const closeBtn = document.getElementById("closeBtn");
const fullscreenBtn = document.getElementById("fullscreenBtn");
const expandBtn = document.getElementById("expandBtn");
const fullPageChat = document.getElementById("fullPageChat");
const backButton = document.getElementById("backButton");
const chatBody = document.getElementById("chatBody");
const fullPageChatBody = document.getElementById("fullPageChatBody");

// Dragging functionality
let isDragging = false;
let offsetX, offsetY;
let initialX, initialY;
let isFullscreen = false;

chatHeader.addEventListener("mousedown", (e) => {
  if (chatPanel.classList.contains("fullscreen")) return;

  isDragging = true;
  const rect = chatPanel.getBoundingClientRect();
  offsetX = e.clientX - rect.left;
  offsetY = e.clientY - rect.top;

  // Store initial position
  initialX = rect.left;
  initialY = rect.top;

  // Change positioning method
  chatPanel.style.position = "fixed";
  chatPanel.style.left = rect.left + "px";
  chatPanel.style.top = rect.top + "px";
  chatPanel.style.bottom = "auto";
  chatPanel.style.right = "auto";
  chatPanel.style.transition = "none";

  e.preventDefault();
});

document.addEventListener("mousemove", (e) => {
  if (isDragging && !chatPanel.classList.contains("fullscreen")) {
    const newX = e.clientX - offsetX;
    const newY = e.clientY - offsetY;

    chatPanel.style.left = newX + "px";
    chatPanel.style.top = newY + "px";
  }
});

document.addEventListener("mouseup", () => {
  if (isDragging) {
    isDragging = false;

    // Check boundaries
    const rect = chatPanel.getBoundingClientRect();
    const viewportWidth = window.innerWidth;
    const viewportHeight = window.innerHeight;

    let newX = parseFloat(chatPanel.style.left);
    let newY = parseFloat(chatPanel.style.top);

    // Adjust if out of viewport
    if (newX < 0) newX = 0;
    if (newX + rect.width > viewportWidth) newX = viewportWidth - rect.width;
    if (newY < 0) newY = 0;
    if (newY + rect.height > viewportHeight)
      newY = viewportHeight - rect.height;

    chatPanel.style.left = newX + "px";
    chatPanel.style.top = newY + "px";
  }
});

// Chat panel toggle
chatButton.addEventListener("click", () => {
  chatPanel.classList.toggle("active");
});

closeBtn.addEventListener("click", (e) => {
  e.stopPropagation();
  chatPanel.classList.remove("active");
});

fullscreenBtn.addEventListener("click", (e) => {
  e.stopPropagation();
  isFullscreen = !isFullscreen;

  if (isFullscreen) {
    // Simulate transition
    chatPanel.style.transition =
      "all 0.4s ease-in-out, transform 0.4s ease-in-out";
    chatPanel.style.transformOrigin = "bottom right";
    chatPanel.style.transform = "scale(1.05)";
    setTimeout(() => {
      chatPanel.classList.add("fullscreen");
      chatPanel.style.transform = "scale(1)";
      chatPanel.style.left = "0";
      chatPanel.style.top = "0";
      chatPanel.style.bottom = "0";
      chatPanel.style.right = "0";
    }, 50);
  } else {
    // Simulate transition
    chatPanel.style.transition =
      "all 0.4s ease-in-out, transform 0.4s ease-in-out";
    chatPanel.style.transformOrigin = "bottom right";
    chatPanel.style.transform = "scale(0.95)";
    setTimeout(() => {
      chatPanel.classList.remove("fullscreen");
      chatPanel.style.left = initialX + "px";
      chatPanel.style.top = initialY + "px";
      chatPanel.style.bottom = "auto";
      chatPanel.style.right = "auto";
      chatPanel.style.transform = "scale(1)";
    }, 300);
  }
});

expandBtn.addEventListener("click", (e) => {
  e.stopPropagation();
  // Copy all messages to full page chat
  fullPageChatBody.innerHTML = chatBody.innerHTML;
  fullPageChat.classList.add("active");
  chatPanel.classList.remove("active");
  // Scroll to bottom
  fullPageChatBody.scrollTop = fullPageChatBody.scrollHeight;

  // Reattach event listeners for negotiation in full page mode
  attachNegotiationListeners("fullPageChatBody");
});

backButton.addEventListener("click", () => {
  // Copy all messages back to normal chat
  chatBody.innerHTML = fullPageChatBody.innerHTML;
  chatPanel.classList.add("active");
  fullPageChat.classList.remove("active");
  // Scroll to bottom
  chatBody.scrollTop = chatBody.scrollHeight;

  // Reattach event listeners for negotiation in normal mode
  attachNegotiationListeners("chatBody");
});

// Message functions
function addUserMessage(text, containerId) {
  const message = document.createElement("div");
  message.className = "message user-message";
  message.textContent = text;
  document.getElementById(containerId).appendChild(message);
  scrollToBottom(containerId);
}

// Modifikasi fungsi addBotMessage
function addBotMessage(text, containerId) {
  const message = document.createElement("div");
  message.className = "message bot-message";

  if (/<[a-z][\s\S]*>/i.test(text)) {
    message.innerHTML = text;
  } else {
    message.textContent = text;
  }

  document.getElementById(containerId).appendChild(message);
  scrollToBottom(containerId);

  // Hanya pasang listeners jika ini adalah pesan negosiasi
  if (
    text.includes("Pilih kendaraan") ||
    text.includes("Anda memilih") ||
    text.includes("Penawaran DITERIMA") ||
    text.includes("Penawaran DITOLAK")
  ) {
    attachNegotiationListeners(containerId);
  }
}

function scrollToBottom(containerId) {
  const container = document.getElementById(containerId);
  container.scrollTop = container.scrollHeight;
}

function sendMessage() {
  const input = document.getElementById("chatInput");
  const message = input.value.trim();
  if (message) {
    addUserMessage(message, "chatBody");
    setTimeout(
      () => addBotMessage("Terima kasih atas pesan Anda! tunggu di jawab oleh admin", "chatBody"),
      500
    );
    input.value = "";
  }
}

function sendFullPageMessage() {
  const input = document.getElementById("fullPageInput");
  const message = input.value.trim();
  if (message) {
    addUserMessage(message, "fullPageChatBody");
    setTimeout(
      () =>
        addBotMessage("Kami akan segera menghubungi Anda.", "fullPageChatBody"),
      500
    );
    input.value = "";
  }
}

// Event listeners for sending messages
document.getElementById("sendButton").addEventListener("click", sendMessage);
document.getElementById("chatInput").addEventListener("keypress", (e) => {
  if (e.key === "Enter") sendMessage();
});

document
  .getElementById("fullPageSendButton")
  .addEventListener("click", sendFullPageMessage);
document.getElementById("fullPageInput").addEventListener("keypress", (e) => {
  if (e.key === "Enter") sendFullPageMessage();
});

// Make sure panel stays within viewport when window is resized
window.addEventListener("resize", () => {
  if (
    !chatPanel.classList.contains("active") ||
    chatPanel.classList.contains("fullscreen")
  )
    return;

  const rect = chatPanel.getBoundingClientRect();
  const viewportWidth = window.innerWidth;
  const viewportHeight = window.innerHeight;

  let needsAdjustment = false;
  let newX = parseFloat(chatPanel.style.left) || initialX || 0;
  let newY = parseFloat(chatPanel.style.top) || initialY || 0;

  if (rect.right > viewportWidth) {
    newX = viewportWidth - rect.width;
    needsAdjustment = true;
  }

  if (rect.bottom > viewportHeight) {
    newY = viewportHeight - rect.height;
    needsAdjustment = true;
  }

  if (rect.left < 0) {
    newX = 0;
    needsAdjustment = true;
  }

  if (rect.top < 0) {
    newY = 0;
    needsAdjustment = true;
  }

  if (needsAdjustment) {
    chatPanel.style.left = newX + "px";
    chatPanel.style.top = newY + "px";
    initialX = newX;
    initialY = newY;
  }
});

// Initialize positions
window.addEventListener("load", () => {
  const rect = chatPanel.getBoundingClientRect();
  initialX = rect.left;
  initialY = rect.top;

  // Attach initial event listeners for promo cards
  attachPromoCardListeners();
});

// Vehicle data
// const vehicles = [{
//         id: 1,
//         name: "Honda Beat Street",
//         price: 18000000,
//         minPrice: 16000000,
//         image: "https://example.com/honda-beat.jpg",
//         description: "Motor matic ekonomis dengan desain sporty"
//     },
//     {
//         id: 2,
//         name: "Yamaha NMAX",
//         price: 28000000,
//         minPrice: 25000000,
//         image: "https://example.com/ymh-nmax.jpg",
//         description: "Skutik premium dengan fitur lengkap"
//     },
//     {
//         id: 3,
//         name: "Suzuki GSX-R150",
//         price: 32000000,
//         minPrice: 30000000,
//         image: "https://example.com/suzuki-gsx.jpg",
//         description: "Sport bike 150cc dengan performa tinggi"
//     }
// ];

// Current negotiation state
let negotiationState = {
  active: false,
  selectedVehicle: null,
  currentOffer: 0,
};

// Attach event listeners to promo cards
function attachPromoCardListeners() {
  document.querySelectorAll(".promo-card[data-target]").forEach((card) => {
    card.addEventListener("click", function () {
      const target = this.getAttribute("data-target");

      if (target === "budget") {
        startNegotiation("chatBody");
      } else {
        // Handle other card types
        addBotMessage(`Fitur ${target} akan segera tersedia!`, "chatBody");
      }
    });
  });
}

function attachNegotiationListeners(containerId) {
  const container = document.getElementById(containerId);

  // Hapus semua event listener yang ada terlebih dahulu
  container.querySelectorAll(".vehicle-option").forEach((option) => {
    option.replaceWith(option.cloneNode(true));
  });

  container.querySelectorAll("#submitOffer").forEach((btn) => {
    btn.replaceWith(btn.cloneNode(true));
  });

  container.querySelectorAll("#newNegotiation").forEach((btn) => {
    btn.replaceWith(btn.cloneNode(true));
  });

  container.querySelectorAll("#tryAgain").forEach((btn) => {
    btn.replaceWith(btn.cloneNode(true));
  });

  // Pasang ulang event listeners
  container.querySelectorAll(".vehicle-option").forEach((option) => {
    option.addEventListener("click", function () {
      const vehicleId = parseInt(this.getAttribute("data-id"));
      selectVehicle(vehicleId, containerId);
    });
  });

  container.querySelectorAll("#submitOffer").forEach((btn) => {
    btn.addEventListener("click", () => submitOffer(containerId));
  });

  container.querySelectorAll("#newNegotiation").forEach((btn) => {
    btn.addEventListener("click", () => resetNegotiation(containerId));
  });

  container.querySelectorAll("#tryAgain").forEach((btn) => {
    btn.addEventListener("click", () => {
      addBotMessage("Silakan masukkan penawaran baru:", containerId);
      createOfferInput(containerId);
    });
  });
}

function startNegotiation(containerId) {
  negotiationState.active = true;

  // Show vehicle selection
  let vehicleOptions = vehicles
    .map(
      (vehicle) =>
        `<div class="vehicle-option" data-id="${vehicle.id}">
                    <h4>${vehicle.name}</h4>
                    <p>Harga: ${vehicle.price.toLocaleString("id-ID")}</p>
                </div>`
    )
    .join("");

  addBotMessage(
    `
                <strong>Pilih kendaraan untuk dinegosiasikan:</strong>
                ${vehicleOptions}
            `,
    containerId
  );

  // Attach event listeners to the newly added vehicle options
  attachNegotiationListeners(containerId);
}

function selectVehicle(vehicleId, containerId) {
  const vehicle = vehicles.find((v) => v.id === vehicleId);
  if (!vehicle) return;

  negotiationState.selectedVehicle = vehicle;

  addBotMessage(
    `
                <strong>Anda memilih ${vehicle.name}</strong>
                <div class="vehicle-card-chat">
                    <h4>${vehicle.name}</h4>
                     <img src="${vehicle.image}" alt="${vehicle.alt}">
                    <p class="description-card">${vehicle.description}</p>
                    <p><strong>Harga:</strong> Rp${vehicle.price.toLocaleString(
                      "id-ID"
                    )}</p>
                </div>
                <p>Silakan masukkan penawaran harga Anda (dalam Rp):</p>
            `,
    containerId
  );

  // Create input for offer
  createOfferInput(containerId);
}

function createOfferInput(containerId) {
  const container = document.getElementById(containerId);

  // Remove any existing offer input
  const existingInput = container.querySelector(".offer-input");
  if (existingInput) {
    existingInput.remove();
  }

  // Create new input
  const inputContainer = document.createElement("div");
  inputContainer.className = "offer-input";
  inputContainer.innerHTML = `
                <input type="number" id="offerInput" placeholder="Masukkan nominal penawaran">
                <button id="submitOffer">Ajukan Penawaran</button>
            `;
  container.appendChild(inputContainer);

  // Attach event listener to the new button
  container.querySelector("#submitOffer").addEventListener("click", () => {
    submitOffer(containerId);
  });

  // Also allow submission by pressing Enter in the input field
  container.querySelector("#offerInput").addEventListener("keypress", (e) => {
    if (e.key === "Enter") {
      submitOffer(containerId);
    }
  });

  scrollToBottom(containerId);
}

function submitOffer(containerId) {
  const container = document.getElementById(containerId);
  const offerInput = container.querySelector("#offerInput");
  const offerAmount = parseInt(offerInput.value.replace(/[^0-9]/g, ""));

  if (isNaN(offerAmount)) {
    addBotMessage("Mohon masukkan angka yang valid", containerId);
    return;
  }

  negotiationState.currentOffer = offerAmount;
  const vehicle = negotiationState.selectedVehicle;

  if (offerAmount >= vehicle.minPrice) {
    addBotMessage(
      `
                    <strong>Penawaran DITERIMA!</strong>
                    <p>Penawaran Anda Rp${offerAmount.toLocaleString(
                      "id-ID"
                    )} untuk ${vehicle.name} diterima.</p>
                    <p>Silakan datang ke dealer kami untuk proses selanjutnya.</p>
                    <button class="negotiation-btn" id="newNegotiation">Negosiasi Baru</button>
                    <button class="negotiation-btn" id="newNegotiation">Pesan Sekarang</button>
                `,
      containerId
    );
  } else {
    addBotMessage(
      `
                    <strong>Penawaran DITOLAK</strong>
                    <p>Maaf, penawaran Rp${offerAmount.toLocaleString(
                      "id-ID"
                    )} terlalu rendah.</p>
                    <button class="negotiation-btn" id="tryAgain">Coba Lagi</button>
                    <button class="negotiation-btn" id="newNegotiation">Pilih Kendaraan Lain</button>
                `,
      containerId
    );
  }

  // Attach event listeners to the new buttons
  attachNegotiationListeners(containerId);
}

function resetNegotiation(containerId) {
  negotiationState = {
    active: false,
    selectedVehicle: null,
    currentOffer: 0,
  };
  startNegotiation(containerId);
}
