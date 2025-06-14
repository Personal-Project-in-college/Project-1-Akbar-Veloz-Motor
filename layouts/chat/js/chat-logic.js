console.log("chat-logic.js active");

document.addEventListener("DOMContentLoaded", async () => {
    // Inisialisasi hanya jika customer sudah login
    if (isCustomerLoggedIn) {
        // Tampilkan tombol chat
        chatButton.style.display = 'flex';
        
        // Event listener utama
        chatButton.addEventListener("click", openChatPanel);
        closeBtn.addEventListener("click", () => chatPanel.classList.remove("active"));
        fullscreenBtn.addEventListener("click", toggleFullScreen);
        backButton.addEventListener("click", toggleFullScreen);

        // Event listener pengiriman pesan
        sendButton.addEventListener("click", () => sendMessage(chatInput, chatBody));
        chatInput.addEventListener("keypress", (e) => { if (e.key === "Enter") sendMessage(chatInput, chatBody); });
        fullPageSendButton.addEventListener("click", () => sendMessage(fullPageInput, fullPageChatBody));
        fullPageInput.addEventListener("keypress", (e) => { if (e.key === "Enter") sendMessage(fullPageInput, fullPageChatBody); });

        // Event listener untuk status mengetik
        chatInput.addEventListener("input", handleTypingInput);
        fullPageInput.addEventListener("input", handleTypingInput);

        // Pasang listener untuk promo card yang sudah ada di HTML
        attachActionListeners(document.body);

    } else {
        // Jika tidak login, sembunyikan tombol chat
        chatButton.style.display = 'none';
    }
});


// =================================================================
// BAGIAN 1: DEFINISI VARIABEL GLOBAL & ELEMEN DOM
// =================================================================

// Elemen DOM
const chatButton = document.getElementById("chatButton");
const chatPanel = document.getElementById("chatPanel");
const closeBtn = document.getElementById("closeBtn");
const chatBody = document.getElementById("chatBody");
const chatInput = document.getElementById("chatInput");
const sendButton = document.getElementById("sendButton");
const adminStatusElement = document.getElementById("adminStatus");
const fullscreenBtn = document.getElementById("fullscreenBtn");
const fullPageChat = document.getElementById("fullPageChat");
const backButton = document.getElementById("backButton");
const fullPageChatBody = document.getElementById("fullPageChatBody");
const fullPageInput = document.getElementById("fullPageInput");
const fullPageSendButton = document.getElementById("fullPageSendButton");

// Variabel State
let vehiclesChat = [];
let chatSessionId = null; // Diambil dari server saat chat dibuka
let isUserOnline = false; // Status online user (admin)
let isCustomerTyping = false;
let typingTimeout;
let isFullScreen = false;
let initialChatLoad = false; // Flag untuk mencegah load history berulang
let pollingInterval;

let negotiationState = {
  active: false,
  selectedVehicle: null,
};


// =================================================================
// BAGIAN 2: KOMUNIKASI DENGAN BACKEND (API)
// =================================================================

async function getOrCreateChatSession() {
    try {
        const response = await fetch("./api/chat.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "get_or_create_session" }),
        });
        const data = await response.json();
        if (data.success) {
            chatSessionId = data.session_id;
            console.log("Sesi chat siap:", chatSessionId);
            return true;
        } else {
            console.error("Gagal mendapatkan sesi chat:", data.message);
            return false;
        }
    } catch (error) {
        console.error("Kesalahan jaringan saat membuat sesi chat:", error);
        return false;
    }
}

async function sendChatMessageToBackend(message, senderType) {
    if (!chatSessionId) return { success: false, message: "Session ID tidak ada." };
    try {
        const response = await fetch("./api/chat.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                action: "send_message",
                message: message,
                session_id: chatSessionId,
                sender_type: senderType, // 'customer', 'bot'
            }),
        });
        return await response.json();
    } catch (error) {
        console.error("Kesalahan saat mengirim pesan ke backend:", error);
        return { success: false, message: "Kesalahan jaringan" };
    }
}

async function loadChatHistory() {
    if (!chatSessionId) return;
    try {
        const response = await fetch(`./api/chat.php?action=get_history&session_id=${chatSessionId}`);
        const data = await response.json();
        if (data.success && data.messages.length > 0) {
            chatBody.innerHTML = ""; // Hapus pesan default
            fullPageChatBody.innerHTML = "";
            data.messages.forEach((msg) => {
                // 'user' di DB adalah admin, 'customer' di DB adalah pelanggan
                const sender = msg.sender_type === 'user' ? 'bot' : msg.sender_type;
                appendMessage(sender, msg.message_text, chatBody.id);
            });
            syncChatBodies();
            console.log("Riwayat chat dimuat.");
        } else {
            console.log("Tidak ada riwayat chat, menampilkan pesan default.");
        }
    } catch (error) {
        console.error("Kesalahan jaringan memuat riwayat chat:", error);
    }
}

async function pollNewMessages() {
    if (!chatSessionId || !chatPanel.classList.contains("active")) return;
    try {
        const response = await fetch(`./api/chat.php?action=get_new_messages&session_id=${chatSessionId}`);
        const data = await response.json();
        if (data.success && data.new_messages.length > 0) {
            removeTypingIndicatorBubble(chatBody); // Hapus indikator jika ada pesan baru
            removeTypingIndicatorBubble(fullPageChatBody);
            data.new_messages.forEach(msg => {
                 // 'user' di DB adalah admin, tampilkan sebagai 'bot'
                const sender = msg.sender_type === 'user' ? 'bot' : msg.sender_type;
                appendMessage(sender, msg.message_text, chatBody.id);
            });
            syncChatBodies();
        }
    } catch (error) {
        console.error("Error polling pesan baru:", error);
    }
}

async function updateUserStatus() {
    try {
        const response = await fetch("./api/chat.php?action=check_user_availability");
        const data = await response.json();
        const statusElement = adminStatusElement; // Nama variabel di HTML
        
        if (data.success) {
            isUserOnline = data.users_available;
            statusElement.textContent = isUserOnline ? "Online" : "Offline";
            statusElement.className = isUserOnline ? "admin-status online" : "admin-status offline";
        } else {
            isUserOnline = false;
            statusElement.textContent = "Status Error";
            statusElement.className = "admin-status offline";
        }
    } catch (error) {
        isUserOnline = false;
        adminStatusElement.textContent = "Offline";
        adminStatusElement.className = "admin-status offline";
    }
}

async function getOpponentTypingStatus() {
    if (!chatSessionId || !chatPanel.classList.contains("active")) return;
    try {
        // 'who=customer' berarti kita (customer) sedang mengecek status lawan (yaitu user/admin)
        const response = await fetch(`./api/chat.php?action=get_typing_status&session_id=${chatSessionId}&who=customer`);
        const data = await response.json();
        if (data.success && data.is_typing) {
            adminStatusElement.textContent = "Admin sedang mengetik...";
            addTypingIndicatorBubble(chatBody.id, 'bot');
            addTypingIndicatorBubble(fullPageChatBody.id, 'bot');
        } else {
            removeTypingIndicatorBubble(chatBody);
            removeTypingIndicatorBubble(fullPageChatBody);
            updateUserStatus(); // Kembalikan ke status online/offline
        }
    } catch (error) {
        // console.error("Gagal mendapatkan status mengetik admin:", error);
    }
}

async function sendTypingStatus(isTyping) {
    if (!chatSessionId || isTyping === isCustomerTyping) return;
    isCustomerTyping = isTyping;
    try {
        await fetch("./api/chat.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                action: "update_typing_status",
                session_id: chatSessionId,
                is_typing: isTyping,
                who: "customer", // Kita adalah 'customer'
            }),
        });
    } catch (error) {
        console.error("Kesalahan saat mengirim status mengetik:", error);
    }
}

async function fetchVehiclesChat() {
  try {
    const response = await fetch("./api/chat.php?action=get_vehicles");
    const data = await response.json();
    if (data.success) {
      vehiclesChat = data.vehicles;
    } else {
      console.error("Gagal mengambil kendaraan:", data.message);
    }
  } catch (error) {
    console.error("Kesalahan jaringan saat mengambil kendaraan:", error);
  }
}


// =================================================================
// BAGIAN 3: LOGIKA TAMPILAN (UI)
// =================================================================

async function openChatPanel() {
    chatPanel.classList.add("active");

    if (!initialChatLoad) {
        const sessionReady = await getOrCreateChatSession();
        if (sessionReady) {
            initialChatLoad = true; // Tandai bahwa inisialisasi sudah dilakukan
            await fetchVehiclesChat();
            await loadChatHistory();
            await updateUserStatus();
            
            // Mulai polling setelah semuanya siap
            if (pollingInterval) clearInterval(pollingInterval);
            pollingInterval = setInterval(() => {
                pollNewMessages();
                updateUserStatus();
                getOpponentTypingStatus();
            }, 3000);
        } else {
            appendMessage('bot', 'Gagal terhubung ke server chat. Silakan coba lagi nanti.', chatBody.id);
        }
    }
}

function appendMessage(sender, text, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    // Hapus indikator mengetik dari lawan sebelum menambahkan pesan baru
    if (sender !== 'customer') {
        removeTypingIndicatorBubble(container);
    }
    
    const messageDiv = document.createElement("div");
    messageDiv.classList.add("message");
    // 'customer' di JS dirender sebagai 'user-message'
    // 'bot' atau 'admin' dirender sebagai 'bot-message'
    const senderClass = sender === "customer" ? "user-message" : "bot-message";
    messageDiv.classList.add(senderClass);
    messageDiv.innerHTML = text; // Teks bisa berisi HTML dari server
    
    container.appendChild(messageDiv);
    container.scrollTop = container.scrollHeight;

    // Pasang listener pada elemen-elemen interaktif yang mungkin baru ditambahkan
    attachActionListeners(messageDiv);
}

function syncChatBodies() {
    if (isFullScreen) {
        fullPageChatBody.innerHTML = chatBody.innerHTML;
        fullPageChatBody.scrollTop = fullPageChatBody.scrollHeight;
        attachActionListeners(fullPageChatBody);
    } else {
        chatBody.innerHTML = fullPageChatBody.innerHTML;
        chatBody.scrollTop = chatBody.scrollHeight;
        attachActionListeners(chatBody);
    }
}

function toggleFullScreen() {
    isFullScreen = !isFullScreen;
    fullPageChat.classList.toggle("active", isFullScreen);
    chatButton.style.display = isFullScreen ? "none" : "flex";
    chatPanel.classList.remove("active");
    if (isFullScreen) {
        syncChatBodies();
    }
}

function addTypingIndicatorBubble(containerId, sender) {
    const container = document.getElementById(containerId);
    if (!container || container.querySelector('.typing-bubble')) return;
    const bubble = document.createElement('div');
    bubble.className = 'message bot-message typing-bubble';
    bubble.innerHTML = '<span class="dot"></span><span class="dot"></span><span class="dot"></span>';
    container.appendChild(bubble);
    container.scrollTop = container.scrollHeight;
}

function removeTypingIndicatorBubble(container) {
    const bubble = container.querySelector('.typing-bubble');
    if (bubble) bubble.remove();
}

function handleTypingInput() {
    if (!chatSessionId) return;
    sendTypingStatus(true);
    clearTimeout(typingTimeout);
    typingTimeout = setTimeout(() => sendTypingStatus(false), 1500);
}

function formatRupiah(angka, prefix) {
    let number_string = angka.replace(/[^,\d]/g, '').toString(),
        split = number_string.split(','),
        sisa = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);
 
    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }
 
    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
}


// =================================================================
// BAGIAN 4: LOGIKA CHATBOT & NEGOSIASI
// =================================================================

async function sendMessage(inputElement, chatBodyElement) {
    const message = inputElement.value.trim();
    if (message === "") return;

    appendMessage("customer", message, chatBodyElement.id);
    if(isFullScreen) appendMessage("customer", message, chatBody.id); // Sync
    inputElement.value = "";
    sendTypingStatus(false);

    // Kirim pesan ke backend sebagai 'customer'
    await sendChatMessageToBackend(message, "customer");
    
    // Jika user (admin) offline, berikan balasan otomatis
    if (!isUserOnline) {
        setTimeout(async () => {
            const offlineMessage = "Terima kasih atas pesan Anda! Saat ini tim kami sedang offline. Kami akan segera merespons setelah kembali online.";
            appendMessage("bot", offlineMessage, chatBodyElement.id);
            syncChatBodies();
            await sendChatMessageToBackend(offlineMessage, "bot");
        }, 1000);
    }
}

function attachActionListeners(container) {
    // Listener untuk promo-card
    container.querySelectorAll(".promo-card[data-target]").forEach((card) => {
        if (card.dataset.listenerAttached) return;
        card.dataset.listenerAttached = "true";
        card.addEventListener("click", (e) => {
            const target = e.currentTarget.dataset.target;
            handlePromoClick(target, e.currentTarget);
        });
    });

    // Listener untuk tombol negosiasi
    container.querySelectorAll(".negotiation-btn[data-action]").forEach((btn) => {
        if (btn.dataset.listenerAttached) return;
        btn.dataset.listenerAttached = "true";
        btn.addEventListener("click", (e) => {
            const action = e.currentTarget.dataset.action;
            handleNegotiationAction(action);
        });
    });
}

async function handlePromoClick(target, element) {
    const userMessage = element.querySelector("h4")?.textContent || `Saya ingin tahu tentang: ${target}`;
    
    appendMessage('customer', userMessage, chatBody.id);
    syncChatBodies();
    await sendChatMessageToBackend(userMessage, 'customer');

    let botMessage = "";
    const currentChatBody = isFullScreen ? fullPageChatBody : chatBody;

    switch (target) {
        case "about":
            botMessage = "Akbar Veloz Motor adalah dealer terpercaya... (informasi lengkap tentang dealer).";
            break;
        case "promo":
            botMessage = "Ini dia promo menarik kami saat ini... (detail promo).";
            break;
        case "budget":
            startNegotiation(currentChatBody.id);
            return; // Stop karena negosiasi punya alur sendiri
        default:
            botMessage = "Maaf, pilihan tidak valid.";
    }
  
    appendMessage("bot", botMessage, currentChatBody.id);
    syncChatBodies();
    await sendChatMessageToBackend(botMessage, "bot");
}

function handleNegotiationAction(action) {
    const currentChatBodyId = isFullScreen ? fullPageChatBody.id : chatBody.id;
    switch(action) {
        case "newNegotiation":
            resetNegotiation(currentChatBodyId);
            break;
        case "tryAgain":
            const msg = "Silakan masukkan penawaran baru Anda:";
            appendMessage("bot", msg, currentChatBodyId);
            sendChatMessageToBackend(msg, "bot");
            createOfferInput(currentChatBodyId);
            syncChatBodies();
            break;
        case "selectOtherVehicle":
            startNegotiation(currentChatBodyId);
            break;
        case "order-negosiasi":
            // Arahkan ke halaman order atau tampilkan modal konfirmasi
            window.location.href = "order.php"; 
            break;
    }
}

async function startNegotiation(containerId) {
    negotiationState.active = true;
    if (vehiclesChat.length === 0) {
        appendMessage("bot", "Maaf, tidak ada kendaraan tersedia untuk negosiasi saat ini.", containerId);
        syncChatBodies();
        return;
    }

    let vehicleOptions = vehiclesChat.map(v =>
        `<div class="promo-card vehicle-option" data-target="vehicle" data-id="${v.id}">
            <h4>${v.name}</h4>
            <img src="${v.image || './assets/images/default-vehicle.png'}" alt="${v.name}" style="width:100px; border-radius:8px;">
            <p>Harga: Rp${Number(v.price).toLocaleString("id-ID")}</p>
        </div>`
    ).join("");

    const messageHtml = `<strong>Pilih kendaraan untuk dinegosiasikan:</strong>${vehicleOptions}`;
    appendMessage("bot", messageHtml, containerId);
    syncChatBodies();
    await sendChatMessageToBackend(messageHtml, "bot");
}

function selectVehicle(vehicleId, containerId) {
    negotiationState.selectedVehicle = vehiclesChat.find(v => v.id == vehicleId);
    const vehicle = negotiationState.selectedVehicle;
    if (!vehicle) return;

    const userMessage = `Saya memilih ${vehicle.name}.`;
    appendMessage('customer', userMessage, containerId);
    sendChatMessageToBackend(userMessage, 'customer');

    const botResponseHtml = `Anda memilih <strong>${vehicle.name}</strong>. Silakan masukkan penawaran Anda:`;
    appendMessage("bot", botResponseHtml, containerId);
    sendChatMessageToBackend(botResponseHtml, "bot");

    createOfferInput(containerId);
    syncChatBodies();
}

function createOfferInput(containerId) {
    const container = document.getElementById(containerId);
    const oldInput = container.querySelector('.offer-input');
    if(oldInput) oldInput.remove();

    const inputDiv = document.createElement('div');
    inputDiv.className = 'offer-input';
    inputDiv.innerHTML = `
        <input type="text" id="offerInput" placeholder="Contoh: 15.000.000">
        <button id="submitOffer">Kirim</button>
    `;
    container.appendChild(inputDiv);
    container.scrollTop = container.scrollHeight;
    
    const offerInput = container.querySelector('#offerInput');
    offerInput.addEventListener('input', (e) => {
        e.target.value = formatRupiah(e.target.value);
    });
    
    container.querySelector('#submitOffer').addEventListener('click', () => submitOffer(containerId));
    offerInput.addEventListener('keypress', (e) => {
        if(e.key === 'Enter') submitOffer(containerId);
    });
}

async function submitOffer(containerId) {
    const container = document.getElementById(containerId);
    const offerInput = container.querySelector("#offerInput");
    const offerAmount = Number(offerInput.value.replace(/[^0-9]/g, ""));
    const vehicle = negotiationState.selectedVehicle;

    if (!vehicle || isNaN(offerAmount) || offerAmount <= 0) {
        appendMessage("bot", "Penawaran tidak valid. Silakan coba lagi.", containerId);
        syncChatBodies();
        return;
    }
    
    container.querySelector('.offer-input').remove();

    const userMessage = `Saya menawarkan Rp ${offerAmount.toLocaleString("id-ID")} untuk ${vehicle.name}.`;
    appendMessage("customer", userMessage, containerId);
    syncChatBodies();

    try {
        const response = await fetch("./api/chat.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                action: "submit_negotiation_offer",
                session_id: chatSessionId,
                vehicle_id: vehicle.id,
                offer_amount: offerAmount,
                customer_message_text: userMessage, // Pesan ini direkam di backend
            }),
        });
        const data = await response.json();
        if (data.success) {
            // Backend akan mengirim HTML balasan yang akan di-append
            appendMessage("bot", data.customer_bot_response_html, containerId);
            syncChatBodies();
        } else {
            appendMessage("bot", `Gagal memproses penawaran: ${data.message}`, containerId);
            syncChatBodies();
        }
    } catch(error) {
        appendMessage("bot", "Terjadi kesalahan jaringan saat mengirim penawaran.", containerId);
        syncChatBodies();
    }
}

function resetNegotiation(containerId) {
    negotiationState = { active: false, selectedVehicle: null };
    const msg = "Baik, mari kita mulai dari awal.";
    appendMessage("bot", msg, containerId);
    sendChatMessageToBackend(msg, "bot");
    startNegotiation(containerId);
    syncChatBodies();
}

// Override listener untuk vehicle-option karena targetnya bukan promo biasa
document.body.addEventListener('click', function(event) {
    const vehicleOption = event.target.closest('.vehicle-option');
    if (vehicleOption) {
        const vehicleId = vehicleOption.getAttribute("data-id");
        const containerId = isFullScreen ? fullPageChatBody.id : chatBody.id;
        selectVehicle(vehicleId, containerId);
    }
});