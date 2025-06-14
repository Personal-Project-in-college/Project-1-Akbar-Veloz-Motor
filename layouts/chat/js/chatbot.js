// layouts/chat/js/chatbot.js

// LOGIKA TAMPILAN + API
// Logika Chatbot (Sisi Klien)

console.log("chatbot aktive");

async function handleBotResponse(target) {
  let botMessage = "";
  const currentChatBodyId = isFullScreen ? fullPageChatBody.id : chatBody.id;

  switch (target) {
    case "about":
      botMessage =
        "Akbar Veloz Motor adalah dealer motor terkemuka yang menyediakan berbagai jenis motor berkualitas tinggi. Kami berkomitmen memberikan pelayanan terbaik dan harga kompetitif. Kunjungi website kami untuk info lebih lanjut!";
      break;
    case "promo":
      botMessage =
        "Jangan lewatkan promo khusus akhir tahun kami! Dapatkan diskon menarik, cashback, dan cicilan ringan untuk motor impian Anda. Penawaran terbatas!";
      break;
    case "budget":
      startNegotiation(currentChatBodyId);
      return; // Jangan kirim pesan bot default di sini, karena negosiasi akan mengirim pesannya sendiri
    default:
      botMessage =
        "Maaf, saya tidak mengerti pilihan Anda. Silakan pilih dari opsi yang tersedia atau ketik pesan Anda.";
  }

  appendMessage("bot", botMessage, currentChatBodyId);
  await sendChatMessageToBackend(botMessage, "bot");
}

// Lampirkan listener awal untuk kartu promo statis saat DOM siap
document.addEventListener("DOMContentLoaded", attachPromoCardListeners);

// Fungsi Pengiriman Pesan (Pengguna ke Admin/Bot)
async function sendMessage(inputElement, chatBodyElement) {
  const message = inputElement.value.trim();
  if (message === "") return;

  if (!chatSessionId) {
    appendMessage(
      "bot",
      "Gagal mengirim pesan: Sesi chat belum terbentuk. Coba ulangi setelah beberapa saat.",
      chatBodyElement.id
    );
    console.error("ID sesi chat tidak diatur. Tidak dapat mengirim pesan.");
    return;
  }

  sendTypingStatus(false);
  appendMessage("customer", message, chatBodyElement.id); // PERBAIKAN: senderType untuk pesan pengguna adalah 'customer'
  inputElement.value = "";

  const lowerCaseMessage = message.toLowerCase();
  const chatbotTriggers = {
    "apa itu akbar veloz motor?": "about",
    "rekomendasi kendaraan akhir tahun": "promo",
    "motor dengan budget murah!": "budget",
  };

  // PERBAIKAN: Kirim pesan pengguna ke backend dengan senderType 'customer'
  const sendResult = await sendChatMessageToBackend(message, "customer");

  // Jika pesan bukan trigger chatbot, dan admin tidak online, kirim pesan bot offline
  if (!chatbotTriggers[lowerCaseMessage]) { // Periksa apakah itu bukan trigger chatbot
    if (sendResult.success && !adminIsOnline) {
      const offlineMessage =
        "Terima kasih atas pesan Anda! Mohon tunggu dijawab oleh admin.";
      appendMessage("bot", offlineMessage, chatBodyElement.id);
      await sendChatMessageToBackend(offlineMessage, "bot");
    }
  } else {
      // Jika itu trigger chatbot, panggil handleBotResponse
      handleBotResponse(chatbotTriggers[lowerCaseMessage]);
  }
}

sendButton.addEventListener("click", () => sendMessage(chatInput, chatBody));
chatInput.addEventListener("keypress", (e) => {
  if (e.key === "Enter") {
    sendMessage(chatInput, chatBody);
  }
});

fullPageSendButton.addEventListener("click", () =>
  sendMessage(fullPageInput, fullPageChatBody)
);
fullPageInput.addEventListener("keypress", (e) => {
  if (e.key === "Enter") {
    sendMessage(fullPageInput, fullPageChatBody);
  }
});

// Event listener untuk mengetik
let lastInputTime = 0;
const TYPING_THRESHOLD_MS = 50;
const IDLE_TIMEOUT_MS = 1500;

function handleInput(event) {
  if (!chatSessionId) return;

  const currentTime = Date.now();
  // PERBAIKAN: Gunakan isCustomerTyping
  if (currentTime - lastInputTime > TYPING_THRESHOLD_MS || !isCustomerTyping) {
    sendTypingStatus(true);
  }
  lastInputTime = currentTime;

  clearTimeout(typingTimeout);
  typingTimeout = setTimeout(() => {
    sendTypingStatus(false);
  }, IDLE_TIMEOUT_MS);
}

chatInput.addEventListener("input", handleInput);
fullPageInput.addEventListener("input", handleInput);

// Logika Khusus Negosiasi
function attachNegotiationListeners(containerId) {
  const container = document.getElementById(containerId);
  if (!container) return;

  container.querySelectorAll(".vehicle-option").forEach((option) => {
    // Hapus listener sebelumnya untuk mencegah duplikasi
    option.onclick = null;
    option.onclick = () => {
      const vehicleId = option.getAttribute("data-id"); // Biarkan sebagai string jika ID CHAR
      console.log("Kendaraan dipilih:", vehicleId);
      selectVehicle(vehicleId, containerId);
    };
  });

  container.querySelectorAll(".negotiation-btn").forEach((btn) => {
    // Hapus listener sebelumnya untuk mencegah duplikasi
    btn.onclick = null;
    btn.onclick = () => {
      const action = btn.dataset.action;
      console.log("Tombol negosiasi diklik:", action);
      if (action === "newNegotiation") {
        resetNegotiation(containerId);
      } else if (action === "tryAgain") {
        appendMessage("bot", "Silakan masukkan penawaran baru:", containerId);
        sendChatMessageToBackend("Silakan masukkan penawaran baru:", "bot"); // Rekam pesan ini di backend
        createOfferInput(containerId);
      } else if (action === "selectOtherVehicle") {
        startNegotiation(containerId);
      } else if (action === "order-negosiasi") {
        window.location.href = "order.php";
      }
    };
  });

  const submitOfferBtn = container.querySelector("#submitOffer");
  if (submitOfferBtn) {
    // Hapus listener sebelumnya untuk mencegah duplikasi
    submitOfferBtn.onclick = null;
    submitOfferBtn.onclick = () => submitOffer(containerId);
  }

  const offerInputElem = container.querySelector("#offerInput");
  if (offerInputElem) {
    // Menghapus listener sebelumnya untuk mencegah duplikasi
    offerInputElem.onkeypress = null;
    offerInputElem.onkeypress = (e) => {
      if (e.key === "Enter") {
        submitOffer(containerId);
      }
    };
    // Menambahkan listener untuk format rupiah secara real-time
    offerInputElem.oninput = formatRupiahInput;
  }
}

async function startNegotiation(containerId) {
  negotiationState.active = true;
  negotiationState.selectedVehicle = null;
  negotiationState.currentOffer = 0;
  console.log("Memulai negosiasi. State:", negotiationState);

  if (vehiclesChat.length === 0) {
    console.log("Kendaraan belum dimuat, mencoba mengambil...");
    await fetchVehiclesChat();
    if (vehiclesChat.length === 0) {
      appendMessage(
        "bot",
        "Maaf, tidak ada kendaraan yang tersedia untuk negosiasi saat ini.",
        containerId
      );
      await sendChatMessageToBackend(
        "Maaf, tidak ada kendaraan yang tersedia untuk negosiasi saat ini.",
        "bot"
      );
      console.error("Tidak ada kendaraan yang tersedia setelah fetch.");
      return;
    }
  }

  let vehicleOptions = vehiclesChat
    .map(
      (vehicle) =>
      `<div class="promo-card vehicle-option" data-id="${vehicle.id}">
<h4>${vehicle.name}</h4>
<img src="${vehicle.image}" alt="${
        vehicle.name
      }" style="max-width:100px; height:auto; border-radius:8px; margin-top:5px;">
<p>Harga: Rp${Number(vehicle.price).toLocaleString("id-ID")}</p>
</div>`
    )
    .join("");

  const messageHtml = `<strong>Pilih kendaraan untuk dinegosiasikan:</strong>${vehicleOptions}`;
  appendMessage("bot", messageHtml, containerId);
  await sendChatMessageToBackend(messageHtml, "bot"); // Kirim ke backend
  attachNegotiationListeners(containerId);
}

function selectVehicle(vehicleId, containerId) {
  // Pastikan vehicleId sesuai dengan tipe data di vehiclesChat (string jika CHAR(8))
  const vehicle = vehiclesChat.find((v) => v.id === vehicleId);
  if (!vehicle) {
    console.error("Kendaraan tidak ditemukan dalam array vehiclesChat:", vehicleId);
    return;
  }

  negotiationState.selectedVehicle = vehicle;
  console.log(
    "Kendaraan dipilih di state negosiasi:",
    negotiationState.selectedVehicle
  );

  const messageHtml = `
<strong>Anda memilih ${vehicle.name}</strong>
<div class="vehicle-card-chat promo-card">
<h4>${vehicle.name}</h4>
<img src="${vehicle.image}" alt="${
    vehicle.name
  }" style="max-width:150px; height:auto; border-radius:8px; margin-top:5px;">
<p class="description-card">${vehicle.description}</p>
<p><strong>Harga:</strong> Rp${Number(vehicle.price).toLocaleString(
    "id-ID"
  )}</p>
</div>
<p>Silakan masukkan penawaran harga Anda (dalam Rp):</p>
`;

  appendMessage("bot", messageHtml, containerId);
  sendChatMessageToBackend(messageHtml, "bot"); // Kirim ke backend
  createOfferInput(containerId);
}

async function submitOffer(containerId) {
  const chatBodyElement = document.getElementById(containerId);
  const offerInput = chatBodyElement.querySelector("#offerInput");
  // Hapus format rupiah sebelum parsing ke Number
  const offerAmount = Number(offerInput.value.replace(/[^0-9]/g, ""));

  console.log("Nilai input penawaran:", offerInput.value);
  console.log("Nominal penawaran setelah parsing:", offerAmount);

  if (isNaN(offerAmount) || offerAmount <= 0) {
    appendMessage(
      "bot",
      "Mohon masukkan angka yang valid dan lebih besar dari nol.",
      containerId
    );
    await sendChatMessageToBackend(
      "Mohon masukkan angka yang valid dan lebih besar dari nol.",
      "bot"
    ); // Kirim pesan error ke backend
    console.warn("Penawaran tidak valid:", offerAmount);
    return;
  }

  if (!negotiationState.selectedVehicle) {
    appendMessage("bot", "Mohon pilih kendaraan terlebih dahulu.", containerId);
    await sendChatMessageToBackend(
      "Mohon pilih kendaraan terlebih dahulu.",
      "bot"
    ); // Kirim pesan error ke backend
    console.error(
      "Tidak ada kendaraan yang dipilih saat mengajukan penawaran."
    );
    return;
  }

  negotiationState.currentOffer = offerAmount;
  const vehicle = negotiationState.selectedVehicle;
  const userMessage = `Saya menawarkan Rp${offerAmount.toLocaleString(
    "id-ID"
  )} untuk ${vehicle.name}.`;

  const offerInputContainer = chatBodyElement.querySelector(".offer-input");
  if (offerInputContainer) {
    offerInputContainer.remove();
    console.log("Input penawaran dihapus dari DOM.");
  }

  // Tampilkan pesan penawaran pengguna di UI lokal secara instan
  appendMessage("customer", userMessage, containerId); // PERBAIKAN: senderType 'customer'
  if (isFullScreen) {
    appendMessage("customer", userMessage, fullPageChatBody.id); // PERBAIKAN: senderType 'customer'
  }

  try {
    console.log("Mengirim penawaran ke backend...");
    console.log("Data yang dikirim ke submit_negotiation_offer:");
    console.log("session_id:", chatSessionId);
    console.log("vehicle_id:", vehicle.id);
    console.log("offer_amount:", offerAmount);
    console.log("customer_message_text:", userMessage); // Ini akan disimpan di chat.php sebagai pesan customer
    console.log("vehicle_name:", vehicle.name);

    const response = await fetch("./api/chat.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        action: "submit_negotiation_offer",
        session_id: chatSessionId,
        vehicle_id: vehicle.id,
        offer_amount: offerAmount,
        customer_message_text: userMessage, // Pesan customer dikirim melalui ini
        vehicle_name: vehicle.name,
      }),
    });

    const data = await response.json();
    console.log("Respons dari submit_negotiation_offer:", data);

    if (data.success) {
      // Hanya append respons bot, pesan user sudah di-append di atas
      appendMessage("bot", data.customer_bot_response_html, containerId);
      if (isFullScreen) {
        appendMessage(
          "bot",
          data.customer_bot_response_html,
          fullPageChatBody.id
        );
      }
      attachNegotiationListeners(chatBody.id);
      if (isFullScreen) attachNegotiationListeners(fullPageChatBody.id);
      console.log(
        "Penawaran berhasil diajukan, balasan bot ditampilkan instan."
      );
    } else {
      appendMessage(
        "bot",
        `Gagal memproses penawaran: ${data.message}`,
        containerId
      );
      await sendChatMessageToBackend(
        `Gagal memproses penawaran: ${data.message}`,
        "bot"
      ); // Kirim pesan error ke backend
      console.error(
        "Server melaporkan kegagalan dalam memproses penawaran:",
        data.message
      );
    }
  } catch (error) {
    console.error("Kesalahan saat mengajukan penawaran ke backend:", error);
    appendMessage(
      "bot",
      "Terjadi kesalahan jaringan saat mengajukan penawaran.",
      containerId
    );
    await sendChatMessageToBackend(
      "Terjadi kesalahan jaringan saat mengajukan penawaran.",
      "bot"
    ); // Kirim pesan error ke backend
  }
}

function resetNegotiation(containerId) {
  negotiationState = {
    active: false,
    selectedVehicle: null,
    currentOffer: 0,
  };
  appendMessage("bot", "Memulai negosiasi baru...", containerId);
  sendChatMessageToBackend("Memulai negosiasi baru...", "bot"); // Rekam pesan ini di backend
  console.log("Negosiasi direset.");
  startNegotiation(containerId);
}

// Inisialisasi sesi chat dan riwayat pada pemuatan halaman
document.addEventListener("DOMContentLoaded", async () => {
  await getOrCreateChatSession();
  await fetchVehiclesChat();
  await loadChatHistory();
  await updateAdminStatus();
  console.log("Halaman dimuat, inisialisasi selesai.");
});