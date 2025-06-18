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
      return;
    default:
      botMessage =
        "Maaf, saya tidak mengerti pilihan Anda. Silakan pilih dari opsi yang tersedia atau ketik pesan Anda.";
  }

  appendMessage("bot", botMessage, currentChatBodyId);
  await sendChatMessageToBackend(botMessage, "bot");
}

document.addEventListener("DOMContentLoaded", attachPromoCardListeners);

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
  appendMessage("customer", message, chatBodyElement.id);
  inputElement.value = "";

  const lowerCaseMessage = message.toLowerCase();
  const chatbotTriggers = {
    "apa itu akbar veloz motor?": "about",
    "rekomendasi kendaraan akhir tahun": "promo",
    "motor dengan budget murah!": "budget",
  };

  const sendResult = await sendChatMessageToBackend(message, "customer");

  if (!chatbotTriggers[lowerCaseMessage]) {
    if (sendResult.success && !adminIsOnline) {
      const offlineMessage =
        "Terima kasih atas pesan Anda! Mohon tunggu dijawab oleh admin.";
      appendMessage("bot", offlineMessage, chatBodyElement.id);
      await sendChatMessageToBackend(offlineMessage, "bot");
    }
  } else {
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

  container.querySelectorAll(".vehicle-option").forEach((option) => {
    option.onclick = null;
    option.onclick = () => {
      const vehicleId = option.getAttribute("data-id");
      console.log("Kendaraan dipilih:", vehicleId);
      selectVehicle(vehicleId, containerId);
    };
  });

  container.querySelectorAll(".negotiation-btn").forEach((btn) => {
    btn.onclick = null;
    btn.onclick = async () => {
      const action = btn.dataset.action;
      const vehicleId = btn.dataset.vehicleId;
      const negotiatedPrice = btn.dataset.negotiatedPrice;

      console.log("Tombol negosiasi diklik:", action);

      if (action === "testDrive") {
        console.log(`Mengajukan Test Drive untuk Vehicle ID: ${vehicleId}, Harga: ${negotiatedPrice}`);

        try {
            const response = await fetch("./api/chat.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    action: "create_test_drive_order",
                    vehicle_id: vehicleId,
                    negotiated_price: negotiatedPrice
                }),
            });
            const data = await response.json();
            if (data.success) {
                appendMessage("bot", `Order Test Drive Anda telah berhasil dibuat untuk kendaraan ${vehicleId}! Tim kami akan segera menghubungi Anda.`, containerId);
                await sendChatMessageToBackend(`Order Test Drive Anda telah berhasil dibuat untuk kendaraan ${vehicleId}! Tim kami akan segera menghubungi Anda.`, "bot");
                window.location.href = "contact-us.php?order_id=" + data.order_id + "&type=test_driver";
            } else {
                appendMessage("bot", `Gagal membuat order Test Drive: ${data.message}`, containerId);
                await sendChatMessageToBackend(`Gagal membuat order Test Drive: ${data.message}`, "bot");
            }
        } catch (error) {
            console.error("Kesalahan jaringan saat membuat order Test Drive:", error);
            appendMessage("bot", "Terjadi kesalahan jaringan saat membuat order Test Drive.", containerId);
            await sendChatMessageToBackend("Terjadi kesalahan jaringan saat membuat order Test Drive.", "bot");
        }
      } else if (action === "continueTransaction") {
        console.log(`Mengajukan Transaksi untuk Vehicle ID: ${vehicleId}, Harga: ${negotiatedPrice}`);

        try {
            const response = await fetch("./api/chat.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    action: "create_transaction_order",
                    vehicle_id: vehicleId,
                    negotiated_price: negotiatedPrice
                }),
            });
            const data = await response.json();
            if (data.success) {
                appendMessage("bot", `Order Transaksi Anda telah berhasil dibuat untuk kendaraan ${vehicleId}! Kami akan mengarahkan Anda ke halaman hubungi kami.`, containerId);
                await sendChatMessageToBackend(`Order Transaksi Anda telah berhasil dibuat untuk kendaraan ${vehicleId}! Kami akan mengarahkan Anda ke halaman hubungi kami.`, "bot");
                window.location.href = "contact-us.php?order_id=" + data.order_id + "&type=transaction";
            } else {
                appendMessage("bot", `Gagal membuat order Transaksi: ${data.message}`, containerId);
                await sendChatMessageToBackend(`Gagal membuat order Transaksi: ${data.message}`, "bot");
            }
        } catch (error) {
            console.error("Kesalahan jaringan saat membuat order Transaksi:", error);
            appendMessage("bot", "Terjadi kesalahan jaringan saat membuat order Transaksi.", containerId);
            await sendChatMessageToBackend("Terjadi kesalahan jaringan saat membuat order Transaksi.", "bot");
        }
      } else if (action === "newNegotiation") {
        resetNegotiation(containerId);
      } else if (action === "tryAgain") {
        appendMessage("bot", "Silakan masukkan penawaran baru:", containerId);
        sendChatMessageToBackend("Silakan masukkan penawaran baru:", "bot");
        createOfferInput(containerId);
      } else if (action === "selectOtherVehicle") {
        startNegotiation(containerId);
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
        `<div class="promo-card vehicle-option" data-id="${vehicle.vehicle_id}">
<h4>${vehicle.vehicle_id} - ${vehicle.brand_name} ${vehicle.model_name}</h4>
<img class="promo-card-img" src="${vehicle.image}" alt="${
          vehicle.brand_name
        } ${vehicle.model_name}" >
<p>Harga: Rp${Number(vehicle.price).toLocaleString("id-ID")}</p>
</div>`
    )
    .join("");

  const messageHtml = `<strong>Pilih kendaraan untuk dinegosiasikan:</strong>${vehicleOptions}`;
  appendMessage("bot", messageHtml, containerId);
  await sendChatMessageToBackend(messageHtml, "bot");
  attachNegotiationListeners(containerId);
}

function selectVehicle(vehicleId, containerId) {
  const vehicle = vehiclesChat.find((v) => v.vehicle_id === vehicleId);
  if (!vehicle) {
    console.error(
      "Kendaraan tidak ditemukan dalam array vehiclesChat:",
      vehicleId
    );
    return;
  }

  negotiationState.selectedVehicle = vehicle;
  console.log(
    "Kendaraan dipilih di state negosiasi:",
    negotiationState.selectedVehicle
  );

{/* <p class="description-card">${vehicle.description}</p> */}

  const messageHtml = `
<strong>Anda memilih ${vehicle.brand_name} ${vehicle.model_name}</strong>
<div class="vehicle-card-chat promo-card">
<h4>${vehicle.vehicle_id} - ${vehicle.brand_name} ${vehicle.model_name}</h4>
<img class="promo-card-img" src="${vehicle.image}" alt="${vehicle.brand_name} ${
    vehicle.model_name
  }" >
<p><strong>Harga:</strong> Rp${Number(vehicle.price).toLocaleString(
    "id-ID"
  )}</p>
</div>
<p>Silakan masukkan penawaran harga Anda (dalam Rp):</p>
`;

  appendMessage("bot", messageHtml, containerId);
  sendChatMessageToBackend(messageHtml, "bot");
  createOfferInput(containerId);
}

async function submitOffer(containerId) {
  const chatBodyElement = document.getElementById(containerId);
  const offerInput = chatBodyElement.querySelector("#offerInput");
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
    );
    console.warn("Penawaran tidak valid:", offerAmount);
    return;
  }

  if (!negotiationState.selectedVehicle) {
    appendMessage("bot", "Mohon pilih kendaraan terlebih dahulu.", containerId);
    await sendChatMessageToBackend(
      "Mohon pilih kendaraan terlebih dahulu.",
      "bot"
    );
    console.error(
      "Tidak ada kendaraan yang dipilih saat mengajukan penawaran."
    );
    return;
  }

  negotiationState.currentOffer = offerAmount;
  const vehicle = negotiationState.selectedVehicle;
  const userMessage = `Saya menawarkan Rp${offerAmount.toLocaleString(
    "id-ID"
  )} untuk ${vehicle.vehicle_id} - ${vehicle.brand_name} ${
    vehicle.model_name
  }.`;

  const offerInputContainer = chatBodyElement.querySelector(".offer-input");
  if (offerInputContainer) {
    offerInputContainer.remove();
    console.log("Input penawaran dihapus dari DOM.");
  }

  appendMessage("customer", userMessage, containerId);
  if (isFullScreen) {
    appendMessage("customer", userMessage, fullPageChatBody.id);
  }

  try {
    console.log("Mengirim penawaran ke backend...");
    console.log("Data yang dikirim ke submit_negotiation_offer:");
    console.log("session_id:", chatSessionId);
    console.log("vehicle_id:", vehicle.vehicle_id);
    console.log("offer_amount:", offerAmount);
    console.log("customer_message_text:", userMessage);
    console.log("vehicle_name:", `${vehicle.brand_name} ${vehicle.model_name}`);
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
    console.log("Respons dari submit_negotiation_offer:", data);

    if (data.success) {
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
      );
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
    );
  }
}

function resetNegotiation(containerId) {
  negotiationState = {
    active: false,
    selectedVehicle: null,
    currentOffer: 0,
  };
  appendMessage("bot", "Memulai negosiasi baru...", containerId);
  sendChatMessageToBackend("Memulai negosiasi baru...", "bot");
  console.log("Negosiasi direset.");
  startNegotiation(containerId);
}

document.addEventListener("DOMContentLoaded", async () => {
  await getOrCreateChatSession();
  await fetchVehiclesChat();
  await loadChatHistory();
  await updateAdminStatus();
  console.log("Halaman dimuat, inisialisasi selesai.");
});