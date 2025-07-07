console.log("chatbot aktive");

// Mengelola respons bot berdasarkan target.
async function handleBotResponse(target) {
  let botMessage = "";

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
      startNegotiation();
      return;
    default:
      botMessage =
        "Maaf, saya tidak mengerti pilihan Anda. Silakan pilih dari opsi yang tersedia atau ketik pesan Anda.";
  }

  // Tambahkan pesan ke kedua kontainer untuk kasus selain budget
  appendMessage("bot", botMessage, chatBody.id);
  appendMessage("bot", botMessage, fullPageChatBody.id);
  await sendChatMessageToBackend(botMessage, "bot");
}

document.addEventListener("DOMContentLoaded", attachPromoCardListeners);

// Mengirim pesan pengguna ke backend dan memproses respons bot.
async function sendMessage(inputElement, chatBodyElement) {
  const message = inputElement.value.trim();
  if (message === "") return;

  if (!chatSessionId) {
    appendMessage(
      "bot",
      "Gagal mengirim pesan: Sesi chat belum terbentuk. Coba ulangi setelah beberapa saat.",
      chatBodyElement.id
    );
    return;
  }

  sendTypingStatus(false);

  appendMessage("customer", message, chatBody.id);
  appendMessage("customer", message, fullPageChatBody.id);

  inputElement.value = "";

  if (!adminIsOnline) {
    addTypingIndicatorBubble(chatBody.id, "bot");
    addTypingIndicatorBubble(fullPageChatBody.id, "bot");
  }

  const sendResult = await sendChatMessageToBackend(message, "customer");

  if (sendResult && sendResult.success && sendResult.ai_messages) {
    sendResult.ai_messages.forEach((aiMsg) => {
      appendMessage(aiMsg.sender_type, aiMsg.message_text, chatBody.id);
      appendMessage(aiMsg.sender_type, aiMsg.message_text, fullPageChatBody.id);
    });
    attachPromoCardListeners();
  }

  const lowerCaseMessage = message.toLowerCase();
  if (lowerCaseMessage === "motor dengan budget murah!") {
    startNegotiation();
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

let lastInputTime = 0;
const TYPING_THRESHOLD_MS = 50;
const IDLE_TIMEOUT_MS = 1500;

function handleInput(event) {
  if (!chatSessionId) return;

  const currentTime = Date.now();
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

function attachNegotiationListeners(containerId) {
  const container = document.getElementById(containerId);
  if (!container) return;

  const searchInput = container.querySelector("#vehicleSearchInput");
  if (searchInput) {
    searchInput.oninput = null;
    searchInput.oninput = (e) =>
      filterNegotiableVehicles(e.target.value, containerId);
  }

  const clearSearchBtn = container.querySelector("#clearSearchButton");
  if (clearSearchBtn) {
    clearSearchBtn.onclick = null;
    clearSearchBtn.onclick = () => {
      if (searchInput) {
        searchInput.value = "";
        filterNegotiableVehicles("", containerId);
        searchInput.focus();
      }
    };
  }

  container.querySelectorAll(".vehicle-option").forEach((option) => {
    option.onclick = null;
    option.onclick = () => {
      const vehicleId = option.getAttribute("data-id");
      selectVehicle(vehicleId);
    };
  });

  container.querySelectorAll(".negotiation-btn").forEach((btn) => {
    btn.onclick = null; 
    btn.onclick = async () => {
      const action = btn.dataset.action;
      const vehicleId = btn.dataset.vehicleId;
      const negotiatedPrice = btn.dataset.negotiatedPrice;

      if (action === "testDrive" || action === "continueTransaction") {
        const typeOrder =
          action === "testDrive" ? "test_driver" : "transaction";
        const url = `contact-us.php?vehicle_id=${encodeURIComponent(
          vehicleId
        )}&type_order=${encodeURIComponent(typeOrder)}`;
        window.location.href = url;
      } else if (action === "newNegotiation") {
        resetNegotiation();
      } else if (action === "tryAgain") {
        const msg = "Silakan masukkan penawaran baru:";
        appendMessage("bot", msg, chatBody.id);
        appendMessage("bot", msg, fullPageChatBody.id);
        await sendChatMessageToBackend(msg, "bot");
        createOfferInput(chatBody.id);
        createOfferInput(fullPageChatBody.id);
      } else if (action === "selectOtherVehicle") {
        startNegotiation();
      }
    };
  });

  const submitOfferBtn = container.querySelector("#submitOffer");
  if (submitOfferBtn) {
    submitOfferBtn.onclick = null;
    submitOfferBtn.onclick = () => submitOffer(containerId);
  }

  const offerInputElem = container.querySelector("#offerInput");
  if (offerInputElem) {
    offerInputElem.onkeypress = null;
    offerInputElem.onkeypress = (e) => {
      if (e.key === "Enter") {
        submitOffer(containerId);
      }
    };
    offerInputElem.oninput = formatRupiahInput;
  }
}

// Memfilter daftar kendaraan yang dapat dinegosiasikan.
function filterNegotiableVehicles(searchTerm, containerId) {
  const container = document.getElementById(containerId);
  const vehicleListContainer = container.querySelector(
    "#negotiableVehicleListContainer"
  );
  const noResultsMessage = container.querySelector("#noVehicleResultsMessage");
  const clearSearchButton = container.querySelector("#clearSearchButton");

  if (!vehicleListContainer) return;

  let visibleCount = 0;
  const lowerCaseSearchTerm = searchTerm.toLowerCase();

  if (lowerCaseSearchTerm.length > 0) {
    if (clearSearchButton) clearSearchButton.style.display = "inline-block";
  } else {
    if (clearSearchButton) clearSearchButton.style.display = "none";
  }

  vehicleListContainer.querySelectorAll(".vehicle-option").forEach((option) => {
    const vehicleName = option.dataset.vehicleName.toLowerCase();
    if (vehicleName.includes(lowerCaseSearchTerm)) {
      option.style.display = "";
      visibleCount++;
    } else {
      option.style.display = "none";
    }
  });

  if (noResultsMessage) {
    if (visibleCount === 0 && lowerCaseSearchTerm.length > 0) {
      noResultsMessage.style.display = "block";
    } else {
      noResultsMessage.style.display = "none";
    }
  }
}

async function startNegotiation() {
  negotiationState.active = true;
  negotiationState.selectedVehicle = null;
  negotiationState.currentOffer = 0;

  if (vehiclesChat.length === 0) {
    await fetchVehiclesChat();
    if (vehiclesChat.length === 0) {
      const msg =
        "Maaf, tidak ada kendaraan yang tersedia untuk negosiasi saat ini.";
      appendMessage("bot", msg, chatBody.id);
      appendMessage("bot", msg, fullPageChatBody.id);
      await sendChatMessageToBackend(msg, "bot");
      return;
    }
  }

  const searchInputHtml = `
      <div class="search-container">
          <input type="text" id="vehicleSearchInput" placeholder="Cari kendaraan...">
          <button id="clearSearchButton" style="display:none;">X</button>
      </div>
      <p id="noVehicleResultsMessage" style="display:none; color: gray; text-align: center; margin-top: 10px;">Tidak ada kendaraan ditemukan.</p>
      <div id="negotiableVehicleListContainer">
          ${vehiclesChat
            .map(
              (vehicle) =>
                `<div class="promo-card vehicle-option" data-id="${
                  vehicle.vehicle_id
                }" data-vehicle-name="${vehicle.display_name}">
                    <h4>${vehicle.display_name}</h4>
                    <img class="promo-card-img" src="${vehicle.image}" alt="${
                  vehicle.brand_name
                } ${vehicle.model_name}" >
                    <p>Harga: Rp${Number(vehicle.price).toLocaleString(
                      "id-ID"
                    )}</p>
                </div>`
            )
            .join("")}
      </div>
    `;

  const messageHtml = `<strong>Pilih kendaraan untuk dinegosiasikan:</strong>${searchInputHtml}`;

  // Tambahkan ke kedua kontainer
  appendMessage("bot", messageHtml, chatBody.id);
  appendMessage("bot", messageHtml, fullPageChatBody.id);
  await sendChatMessageToBackend(messageHtml, "bot");

  // Pasang listener ke kedua kontainer
  attachNegotiationListeners(chatBody.id);
  attachNegotiationListeners(fullPageChatBody.id);
}

function selectVehicle(vehicleId) {
  const vehicle = vehiclesChat.find((v) => v.vehicle_id === vehicleId);
  if (!vehicle) return;

  negotiationState.selectedVehicle = vehicle;

  const messageHtml = `
      <strong>Anda memilih ${vehicle.brand_name} ${vehicle.model_name}</strong>
      <div class="vehicle-card-chat promo-card">
        <h4>${vehicle.display_name}</h4>
        <img class="promo-card-img" src="${vehicle.image}" alt="${
    vehicle.brand_name
  } ${vehicle.model_name}" >
        <p><strong>Harga:</strong> Rp${Number(vehicle.price).toLocaleString(
          "id-ID"
        )}</p>
      </div>
      <p>Silakan masukkan penawaran harga Anda (dalam Rp):</p>
    `;

  appendMessage("bot", messageHtml, chatBody.id);
  appendMessage("bot", messageHtml, fullPageChatBody.id);
  sendChatMessageToBackend(messageHtml, "bot");

  // Buat input di kedua kontainer
  createOfferInput(chatBody.id);
  createOfferInput(fullPageChatBody.id);
}

async function submitOffer(containerId) {
  const mainContainer = document.getElementById(containerId);
  if (!mainContainer) return;

  const offerInput = mainContainer.querySelector("#offerInput");
  if (!offerInput || !negotiationState.selectedVehicle) return;

  const offerAmount = Number(offerInput.value.replace(/[^0-9]/g, ""));
  if (isNaN(offerAmount) || offerAmount <= 0) {
    const msg = "Mohon masukkan angka yang valid dan lebih besar dari nol.";
    appendMessage("bot", msg, chatBody.id);
    appendMessage("bot", msg, fullPageChatBody.id);
    await sendChatMessageToBackend(msg, "bot");
    return;
  }

  negotiationState.currentOffer = offerAmount;
  const vehicle = negotiationState.selectedVehicle;
  const userMessage = `Saya menawarkan Rp${offerAmount.toLocaleString(
    "id-ID"
  )} untuk ${vehicle.display_name}.`;

  // Hapus input dari kedua kontainer
  document.querySelectorAll(".offer-input").forEach((el) => el.remove());

  appendMessage("customer", userMessage, chatBody.id);
  appendMessage("customer", userMessage, fullPageChatBody.id);

  const response = await fetch("./api/chat.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      action: "submit_negotiation_offer",
      session_id: chatSessionId,
      vehicle_id: vehicle.vehicle_id,
      offer_amount: offerAmount,
      customer_message_text: userMessage,
      vehicle_name: `${vehicle.brand_name} ${vehicle.model_name}`,
    }),
  });
  const data = await response.json();

  if (data.success) {
    appendMessage("bot", data.customer_bot_response_html, chatBody.id);
    appendMessage("bot", data.customer_bot_response_html, fullPageChatBody.id);
    attachPromoCardListeners(); // Re-attach untuk tombol baru
  } else {
    const msg = `Gagal memproses penawaran: ${data.message}`;
    appendMessage("bot", msg, chatBody.id);
    appendMessage("bot", msg, fullPageChatBody.id);
    await sendChatMessageToBackend(msg, "bot");
  }
}

function resetNegotiation() {
  negotiationState.active = false;
  const msg = "Memulai negosiasi baru...";
  appendMessage("bot", msg, chatBody.id);
  appendMessage("bot", msg, fullPageChatBody.id);
  sendChatMessageToBackend(msg, "bot");
  startNegotiation();
}

document.addEventListener("DOMContentLoaded", async () => {
  await getOrCreateChatSession();
  await fetchVehiclesChat();
  await loadChatHistory();
  await updateAdminStatus();
  console.log("Halaman dimuat, inisialisasi selesai.");
});
