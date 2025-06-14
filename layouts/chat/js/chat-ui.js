// layouts/chat/js/chat-ui.js
// Variabel elemen DOM

console.log("chat-ui-aktive");

const chatButton = document.getElementById("chatButton");
const chatPanel = document.getElementById("chatPanel");
const closeBtn = document.getElementById("closeBtn");
const fullscreenBtn = document.getElementById("fullscreenBtn");
const fullPageChat = document.getElementById("fullPageChat");
const backButton = document.getElementById("backButton");
const chatHeader = document.getElementById("chatHeader");
const chatBody = document.getElementById("chatBody");
const fullPageChatBody = document.getElementById("fullPageChatBody");
const chatInput = document.getElementById("chatInput");
const sendButton = document.getElementById("sendButton");
const fullPageInput = document.getElementById("fullPageInput");
const fullPageSendButton = document.getElementById("fullPageSendButton");
const adminStatusElement = document.getElementById("adminStatus");

// Variabel status global
let isDragging = false;
let offsetX, offsetY;
let isFullScreen = false;
let initialX, initialY; // Posisi awal panel chat (untuk draggable)

// Variabel API (chatSessionId sudah didefinisikan di chat_widget.php)
let adminIsOnline = false; // Status admin online/offline
// let chatSessionId = '<?php echo $_SESSION["chat_session_id"] ?? ""; ?>'; // INI DIPINDAH KE chat_widget.php AGAR NILAINYA BENAR TER-RENDER OLEH PHP

let typingTimeout;
let isCustomerTyping = false; // Status customer sedang mengetik, diubah dari isUserTyping
let vehiclesChat = []; // Data kendaraan
let negotiationState = {
  active: false,
  selectedVehicle: null,
  currentOffer: 0,
};

// TAMPILAN
// Fungsi UI Chat
function appendMessage(sender, message, containerId) {
  const bodyElement = document.getElementById(containerId);
  if (!bodyElement) {
    console.error(`Elemen dengan ID '${containerId}' tidak ditemukan.`);
    return;
  }

  const messageDiv = document.createElement("div");
  messageDiv.classList.add("message");
  // Sender type dari backend adalah 'customer', 'user' (untuk admin), atau 'bot'
  if (sender === "customer") { // Jika pengirim adalah customer (pengguna tampilan ini)
    messageDiv.classList.add("user-message"); // Gunakan CSS user-message
  } else if (sender === "bot" || sender === "user") { // Jika pengirim adalah bot atau admin (user)
    messageDiv.classList.add("bot-message"); // Gunakan CSS bot-message
  }

  messageDiv.innerHTML = message;
  bodyElement.appendChild(messageDiv);
  bodyElement.scrollTop = bodyElement.scrollHeight; // Gulir ke bawah
}

function addTypingIndicatorBubble(containerId, senderType) {
  const bodyElement = document.getElementById(containerId);
  if (!bodyElement) return;

  const existingTypingBubble = bodyElement.querySelector(".typing-bubble");
  if (existingTypingBubble) {
    // Jika gelembung mengetik sudah ada dan itu untuk sender yang sama,
    // cukup pastikan animasinya tidak sedang "shrink-out"
    if (existingTypingBubble.dataset.senderType === senderType) {
      existingTypingBubble.classList.remove("shrink-out");
      return;
    } else {
      // Jika ada gelembung mengetik tapi dari sender yang berbeda, hapus yang lama
      existingTypingBubble.remove();
    }
  }

  const typingBubble = document.createElement("div");
  typingBubble.classList.add("message", "typing-bubble");
  typingBubble.dataset.senderType = senderType; // Simpan tipe pengirim di dataset

  // Tentukan kelas CSS berdasarkan tipe pengirim untuk styling yang benar
  if (senderType === "user" || senderType === "bot") { // Admin (user) atau bot
    typingBubble.classList.add("bot-message"); // Tampilkan di sisi "bot" (kiri)
  } else if (senderType === "customer") { // Customer (pengguna tampilan ini)
    typingBubble.classList.add("user-message"); // Tampilkan di sisi "user" (kanan)
  }

  typingBubble.innerHTML = `<span class="dot"></span><span class="dot"></span><span class="dot"></span>`;
  bodyElement.appendChild(typingBubble);
  bodyElement.scrollTop = bodyElement.scrollHeight;
}


function removeTypingIndicatorBubble(containerId) {
  const bodyElement = document.getElementById(containerId);
  if (!bodyElement) return;

  const typingBubble = bodyElement.querySelector(".typing-bubble");
  if (typingBubble) {
    typingBubble.classList.add("shrink-out");
    setTimeout(() => {
      typingBubble.remove();
    }, 300); // Durasi harus sesuai dengan durasi transisi CSS 'shrink-out'
  }
}

// Override appendChild untuk menghapus indikator mengetik saat pesan masuk.
const originalAppendChild = Node.prototype.appendChild;
Node.prototype.appendChild = function (newNode) {
  if (
    (this === chatBody || this === fullPageChatBody) &&
    newNode.classList.contains("message") &&
    !newNode.classList.contains("typing-bubble") &&
    // Jika pesan masuk BUKAN dari 'user-message' (yaitu dari bot atau admin),
    // maka hapus indikator mengetik lawan (yang biasanya admin/bot).
    // Jika pesan masuk adalah 'user-message', itu dari customer itu sendiri,
    // jadi indikator mengetik admin/bot tidak perlu dihapus karena pesan customer ini.
    // Kita hapus indikator mengetik 'customer' jika ada.
    !newNode.classList.contains("user-message")
  ) {
    // Hapus indikator mengetik jika pesan yang masuk bukan dari customer itu sendiri
    removeTypingIndicatorBubble(this.id);
  } else if (
    (this === chatBody || this === fullPageChatBody) &&
    newNode.classList.contains("message") &&
    newNode.classList.contains("user-message") // Jika pesan yang masuk adalah user-message (dari customer)
  ) {
    // Hapus indikator mengetik customer jika ada (karena customer sudah mengirim pesan)
    // Walaupun sendTypingStatus(false) seharusnya sudah menangani ini
    removeTypingIndicatorBubble(this.id);
  }
  return originalAppendChild.call(this, newNode);
};

// Melampirkan event listener untuk kartu promo dan tombol negosiasi.
function attachPromoCardListeners() {
  const attachListeners = (containerId) => {
    const container = document.getElementById(containerId);
    if (!container) return;

    container.querySelectorAll(".promo-card").forEach((card) => {
      // Hapus listener sebelumnya untuk mencegah duplikasi
      card.onclick = null;
      card.onclick = async () => {
        const target = card.dataset.target;
        const userMessageContent = card.querySelector("h4") ?
          card.querySelector("h4").textContent :
          card.textContent.trim();

        // Tampilkan pesan pengguna di UI lokal
        appendMessage("customer", userMessageContent, chatBody.id); // Customer mengirim
        if (isFullScreen)
          appendMessage("customer", userMessageContent, fullPageChatBody.id);

        // Kirim pesan pengguna ke backend sebagai pesan biasa
        await sendChatMessageToBackend(userMessageContent, "customer"); // Customer mengirim

        // Tangani respons bot berdasarkan target promo
        handleBotResponse(target);
      };
    });
    attachNegotiationListeners(containerId);
  };

  attachListeners(chatBody.id);
  if (isFullScreen) attachListeners(fullPageChatBody.id);
}

function createOfferInput(containerId) {
  const chatBodyElement = document.getElementById(containerId);
  if (!chatBodyElement) return;

  const existingInput = chatBodyElement.querySelector(".offer-input");
  if (existingInput) {
    existingInput.remove();
  }

  const inputContainer = document.createElement("div");
  inputContainer.className = "offer-input";
  inputContainer.innerHTML = `
<input type="text" id="offerInput" placeholder="Masukkan nominal penawaran">
<button id="submitOffer">Ajukan Penawaran</button>
`;

  chatBodyElement.appendChild(inputContainer);
  console.log("Input penawaran dibuat.");
  attachNegotiationListeners(containerId);
  chatBodyElement.scrollTop = chatBodyElement.scrollHeight;
}

function formatRupiahInput(event) {
  let input = event.target.value;
  // Hapus semua karakter non-digit
  input = input.replace(/[^0-9]/g, "");

  // Jika input kosong atau hanya nol, set value ke kosong
  if (input === "" || input === "0") {
    event.target.value = "";
    return;
  }

  // Konversi ke Number untuk memastikan tidak ada leading zeros yang tidak perlu
  let number = parseInt(input, 10);
  if (isNaN(number)) {
    event.target.value = "";
    return;
  }

  // Format angka menjadi string dengan pemisah ribuan
  event.target.value = new Intl.NumberFormat("id-ID").format(number);
}

// Logika UI Panel Chat
chatButton.addEventListener("click", async () => {
  if (!isCustomerLoggedIn) {
    alert("Silakan login terlebih dahulu untuk menggunakan fitur chat.");
    window.location.href = "login.php";
    return;
  }

  chatPanel.classList.toggle("active");

  if (chatPanel.classList.contains("active")) {
    await getOrCreateChatSession();
    await loadChatHistory();
    await updateAdminStatus();
  }
});


backButton.addEventListener("click", () => {
  fullPageChat.classList.remove("active");
  chatPanel.classList.remove("fullscreen");
  isFullScreen = false;
  sendTypingStatus(false); // Hentikan status mengetik customer
  removeTypingIndicatorBubble(chatBody.id);
  removeTypingIndicatorBubble(fullPageChatBody.id);
});

// Fungsionalitas Drag untuk panel chat (saat tidak fullscreen)
chatHeader.addEventListener("mousedown", (e) => {
  if (chatPanel.classList.contains("fullscreen")) return;

  isDragging = true;
  const rect = chatPanel.getBoundingClientRect();
  offsetX = e.clientX - rect.left;
  offsetY = e.clientY - rect.top;

  initialX = rect.left;
  initialY = rect.top;

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
    const rect = chatPanel.getBoundingClientRect();
    const viewportWidth = window.innerWidth;
    const viewportHeight = window.innerHeight;

    let newX = parseFloat(chatPanel.style.left);
    let newY = parseFloat(chatPanel.style.top);

    if (newX < 0) newX = 0;
    if (newX + rect.width > viewportWidth) newX = viewportWidth - rect.width;
    if (newY < 0) newY = 0;
    if (newY + rect.height > viewportHeight)
      newY = viewportHeight - rect.height;

    chatPanel.style.left = newX + "px";
    chatPanel.style.top = newY + "px";
  }
});

closeBtn.addEventListener("click", (e) => {
  e.stopPropagation();
  chatPanel.classList.remove("active");
});

fullscreenBtn.addEventListener("click", (e) => {
  e.stopPropagation();
  isFullScreen = !isFullScreen;

  if (isFullScreen) {
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
    // Ubah ikon saat masuk mode fullscreen
    fullscreenBtn.innerHTML = `<span>
<svg id="minimize" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="#ffffff" stroke="#ffffff"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title></title> <g id="Complete"> <g id="minimize"> <g> <path d="M8,3V6A2,2,0,0,1,6,8H3" fill="none" stroke="#ffffff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path> <path d="M16,21V18a2,2,0,0,1,2-2h3" fill="none" stroke="#ffffff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path> <path d="M8,21V18a2,2,0,0,0-2-2H3" fill="none" stroke="#ffffff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path> <path d="M16,3V6a2,2,0,0,0,2,2h3" fill="none" stroke="#ffffff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path> </g> </g> </g> </g></svg>
</span>`; // Contoh ikon "minimize"
  } else {
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
    // Ubah ikon kembali saat keluar mode fullscreen
    fullscreenBtn.innerHTML = `<span>
<svg id="iconFullscreenBtn" viewBox="0 0 20 20" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="white">
<g id="SVGRepo_bgCarrier" stroke-width="0"></g>
<g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
<g id="SVGRepo_iconCarrier">
<title>full_screen [#904]</title>
<desc>Created with Sketch.</desc>
<defs> </defs>
<g id="Page-1" stroke="none" stroke-width="1" fill="white" fill-rule="evenodd">
<g id="Dribbble-Light-Preview" transform="translate(-300.000000, -4199.000000)" fill="white">
<g id="icons" transform="translate(56.000000, 160.000000)">
<path d="M262.4445,4039 L256.0005,4039 L256.0005,4041 L262.0005,4041 L262.0005,4047 L264.0005,4047 L264.0005,4039.955 L264.0005,4039 L262.4445,4039 Z M262.0005,4057 L256.0005,4057 L256.0005,4059 L262.4445,4059 L264.0005,4059 L264.0005,4055.955 L264.0005,4051 L262.0005,4051 L262.0005,4057 Z M246.0005,4051 L244.0005,4051 L244.0005,4055.955 L244.0005,4059 L246.4445,4059 L252.0005,4059 L252.0005,4057 L246.0005,4057 L246.0005,4051 Z M246.0005,4047 L244.0005,4047 L244.0005,4039.955 L244.0005,4039 L246.4445,4039 L252.0005,4039 L252.0005,4041 L246.0005,4041 L246.0005,4047 Z" id="full_screen-[#904]"> </path>
</g>
</g>
</g>
</g>
</svg>
</span>`;
  }
});

// Back button pada chat fullscreen.
backButton.addEventListener("click", () => {
  // Salin pesan dari chat fullscreen ke chat biasa
  chatBody.innerHTML = fullPageChatBody.innerHTML;
  chatPanel.classList.add("active"); // Pastikan panel utama aktif
  fullPageChat.classList.remove("active"); // Sembunyikan chat fullscreen
  chatPanel.classList.remove("fullscreen"); // Hapus class fullscreen dari panel utama
  isFullScreen = false; // Reset status fullscreen
  chatBody.scrollTop = chatBody.scrollHeight; // Gulir ke bawah
  attachNegotiationListeners(chatBody.id); // Pasang ulang listener untuk chat biasa
  sendTypingStatus(false); // Hentikan status mengetik customer
  removeTypingIndicatorBubble(chatBody.id); // Hapus indikator mengetik
  removeTypingIndicatorBubble(fullPageChatBody.id); // Hapus indikator mengetik
});

// Sesuaikan posisi panel saat ukuran jendela diubah
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

window.addEventListener("load", () => {
  const rect = chatPanel.getBoundingClientRect();
  initialX = rect.left;
  initialY = rect.top;
  attachPromoCardListeners(); // tunggu DOM sepenuhnya siap baru pasang listener
});