// layouts/chat/js/koneksi-api-chat.js

// LOGIKA API

console.log("koneksi chat aktive");


async function fetchVehiclesChat() {
  try {
    const response = await fetch("./api/chat.php?action=get_vehicles");
    const data = await response.json();
    if (data.success) {
      // PERBAIKAN: Ganti 'vehicles' menjadi 'vehiclesChat' (nama variabel global yang benar)
      vehiclesChat = data.vehicles;
      console.log("Kendaraan berhasil diambil:", vehiclesChat);
    } else {
      console.error("Gagal mengambil kendaraan:", data.message);
      const errorMessage =
        "Gagal memuat daftar kendaraan. Silakan coba lagi nanti.";
      appendMessage("bot", errorMessage, chatBody.id);
      if (isFullScreen) appendMessage("bot", errorMessage, fullPageChatBody.id);
    }
  } catch (error) {
    console.error("Kesalahan jaringan saat mengambil kendaraan:", error);
    const errorMessage =
      "Terjadi kesalahan jaringan saat memuat daftar kendaraan.";
    appendMessage("bot", errorMessage, chatBody.id);
    if (isFullScreen) appendMessage("bot", errorMessage, fullPageChatBody.id);
  }
}

// Fungsi Inti Pengiriman Pesan ke Backend
// Menerima senderType yang benar dari pemanggil (misalnya 'customer' atau 'bot')
async function sendChatMessageToBackend(message, senderType) {
  try {
    const response = await fetch("./api/chat.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        action: "send_message",
        message: message,
        session_id: chatSessionId,
        sender_type: senderType, // Gunakan senderType yang diterima
      }),
    });
    const data = await response.json();
    if (!data.success) {
      console.error("Gagal mengirim pesan ke backend:", data.message);
    }
    return data;
  } catch (error) {
    console.error("Kesalahan saat mengirim pesan ke backend:", error);
    return {
      success: false,
      message: "Kesalahan jaringan"
    };
  }
}

// Logika Status Mengetik (Sisi Customer)
async function sendTypingStatus(isTyping) {
  if (!chatSessionId) return;

  // PERBAIKAN: Gunakan isCustomerTyping, bukan isUserTyping (yang mengacu pada admin)
  if (isTyping !== isCustomerTyping) {
    isCustomerTyping = isTyping;
    try {
      await fetch("./api/chat.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          action: "update_typing_status",
          session_id: chatSessionId,
          is_typing: isTyping,
          who: "customer", // PERBAIKAN: Pengirim status mengetik adalah 'customer'
        }),
      });
      console.log("Status mengetik customer dikirim:", isTyping);
    } catch (error) {
      console.error("Kesalahan saat mengirim status mengetik customer:", error);
    }
  }
}

// Manajemen Sesi Chat
async function getOrCreateChatSession() {
  // Pastikan chatSessionId yang didapat dari PHP sudah benar
  if (chatSessionId && chatSessionId !== '<?php echo $_SESSION["chat_session_id"] ?? ""; ?>' && chatSessionId !== '') {
    console.log("Sesi chat yang ada (variabel JS):", chatSessionId);
    try {
      const response = await fetch("./api/chat.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          action: "check_session",
          session_id: chatSessionId,
        }),
      });
      const data = await response.json();
      if (!data.success) {
        chatSessionId = null; // Set null agar sesi baru dibuat
        console.warn(
          "Sesi chat yang ada tidak valid atau ditutup, membuat yang baru."
        );
      } else {
        console.log("Sesi chat dari PHP valid dan aktif.");
      }
    } catch (error) {
      console.error("Kesalahan memeriksa sesi yang ada:", error);
      chatSessionId = null; // Set null agar sesi baru dibuat
    }
  }

  if (!chatSessionId) {
    try {
      const response = await fetch("./api/chat.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          action: "get_or_create_session"
        }),
      });
      const data = await response.json();
      if (data.success) {
        chatSessionId = data.session_id;
        console.log("Sesi chat baru dibuat:", chatSessionId);
      } else {
        console.error(
          "Gagal mendapatkan atau membuat sesi chat:",
          data.message
        );
      }
    } catch (error) {
      console.error("Kesalahan jaringan saat membuat sesi chat:", error);
    }
  }
}

// Memuat Riwayat Chat
async function loadChatHistory() {
  if (!chatSessionId) return;

  try {
    const response = await fetch(
      `./api/chat.php?action=get_history&session_id=${chatSessionId}`
    );
    const data = await response.json();

    if (data.success) {
      chatBody.innerHTML = "";
      fullPageChatBody.innerHTML = "";

      const initialBotMessagesHTML = `
<div class="message bot-message">Halo! Butuh bantuan?</div>
<div class="message bot-message">
<strong>Ngobrol dengan kami sekarang!</strong>
</div>
<div class="message bot-message">
<strong>Lebih dekat dengan Veloz!</strong>
<div class="promo-card" data-target="about">
<h4>Apa itu Akbar Veloz Motor?</h4>
<p>Temukan semua informasi tentang produk kami</p>
</div>
<div class="promo-card" data-target="promo">
<h4>Rekomendasi Kendaraan Akhir Tahun</h4>
<p>Lihat promo khusus akhir tahun</p>
</div>
<div class="promo-card" data-target="budget">
<h4>Motor dengan Budget Murah!</h4>
<p>Temukan motor sesuai budget Anda</p>
</div>
</div>
<div class="message bot-message">
<strong>Berbicara dengan chatbot agent</strong>
</div>
`;

      chatBody.innerHTML = initialBotMessagesHTML;
      fullPageChatBody.innerHTML = initialBotMessagesHTML;

      attachPromoCardListeners(); // Lampirkan listener untuk elemen statis awal (promosi)

      data.messages.forEach((msg) => {
        // Logika untuk mencegah duplikasi pesan awal statis
        // PERBAIKAN: Gunakan pendekatan yang lebih robust untuk menghindari duplikasi
        // daripada hanya includes(), karena HTML bisa bervariasi spasi/newline.
        // Cek apakah pesan sudah ditambahkan sebagai bagian dari initialBotMessagesHTML
        // Pesan-pesan ini harus memiliki content yang sama persis dengan yang sudah statis.
        const staticMessages = [
            "Halo! Butuh bantuan?",
            "Ngobrol dengan kami sekarang!",
            "Berbicara dengan chatbot agent"
        ];
        // Cek apakah message_text sesuai dengan salah satu staticMessages
        const isStaticBotMessage = staticMessages.includes(msg.message_text.trim());

        // Untuk promo-card, cek apakah pesan berasal dari bot DAN mengandung struktur promo-card
        const isPromoCardBotResponse = msg.sender_type === "bot" && msg.message_text.includes("promo-card");

        // Pesan negosiasi dan pesan yang dikirim dinamis harus selalu ditambahkan ulang
        const isNegotiationResponse =
          msg.message_text.includes("Penawaran DITERIMA!") ||
          msg.message_text.includes("Penawaran DITOLAK") ||
          msg.message_text.includes("Pilih kendaraan untuk dinegosiasikan:") ||
          msg.message_text.includes("Silakan masukkan penawaran baru:") ||
          msg.message_text.includes("Memulai negosiasi baru...");

        // Tambahkan pesan jika:
        // 1. Bukan pesan bot statis
        // 2. Bukan pesan promo-card bot yang sudah ada secara statis
        // 3. Adalah pesan negosiasi (harus selalu ditambahkan)
        // 4. Adalah pesan dari 'customer' (sebelumnya 'user') atau 'user' (sebelumnya 'admin')
        if (
            (!isStaticBotMessage && !isPromoCardBotResponse) || // Ini menangani pesan bot yang bukan statis awal
            isNegotiationResponse || // Selalu tambahkan pesan negosiasi
            msg.sender_type === "customer" || // Selalu tambahkan pesan dari customer
            msg.sender_type === "user" // Selalu tambahkan pesan dari user (admin)
        ) {
          appendMessage(msg.sender_type, msg.message_text, chatBody.id);
          if (isFullScreen) {
            appendMessage(
              msg.sender_type,
              msg.message_text,
              fullPageChatBody.id
            );
          }
          // Attach listeners for newly appended messages
          attachNegotiationListeners(chatBody.id);
          if (isFullScreen) attachNegotiationListeners(fullPageChatBody.id);
        }
      });
      console.log("Riwayat chat dimuat untuk sesi:", chatSessionId);
    } else {
      console.error("Gagal memuat riwayat chat:", data.message);
    }
  } catch (error) {
    console.error("Kesalahan jaringan memuat riwayat chat:", error);
  }
}

// Polling Status Admin
async function updateAdminStatus() {
  try {
    const response = await fetch(
      "./api/chat.php?action=check_admin_availability"
    );
    const data = await response.json();
    if (data.success) {
      adminIsOnline = data.users_available; // PERBAIKAN: Sesuaikan dengan nama properti di respons API
      if (adminIsOnline) {
        adminStatusElement.textContent = "Online";
        adminStatusElement.classList.remove("offline");
        adminStatusElement.classList.add("online"); // Tambahkan kelas 'online'
      } else {
        adminStatusElement.textContent = "Offline";
        adminStatusElement.classList.remove("online"); // Hapus kelas 'online'
        adminStatusElement.classList.add("offline");
      }
    } else {
      adminStatusElement.textContent = "Status tidak diketahui";
      adminStatusElement.classList.add("offline");
      console.error("Gagal mendapatkan status admin:", data.message);
    }
  } catch (error) {
    adminStatusElement.textContent = "Offline (Error)";
    adminStatusElement.classList.add("offline");
    console.error("Kesalahan jaringan memeriksa status admin:", error);
  }
}

// Polling Status Mengetik Lawan (Admin)
async function getOpponentTypingStatus() {
  if (!chatSessionId || !chatPanel.classList.contains("active")) return;

  try {
    const response = await fetch(
      `./api/chat.php?action=get_typing_status&session_id=${chatSessionId}&who=customer` // PERBAIKAN: Parameter 'who' adalah customer
    );
    const data = await response.json();
    if (data.success) {
      if (data.is_typing) {
        adminStatusElement.textContent = "Mengetik...";
        adminStatusElement.classList.add("typing");
        adminStatusElement.classList.remove("offline", "online");
        addTypingIndicatorBubble(chatBody.id, "user"); // Indikator mengetik untuk "admin" (user)
        if (isFullScreen)
          addTypingIndicatorBubble(fullPageChatBody.id, "user"); // Indikator mengetik untuk "admin" (user)
      } else {
        removeTypingIndicatorBubble(chatBody.id);
        if (isFullScreen) removeTypingIndicatorBubble(fullPageChatBody.id);
        updateAdminStatus(); // Periksa kembali status umum (online/offline)
        adminStatusElement.classList.remove("typing");
      }
    } else {
      console.error("Gagal mendapatkan status mengetik lawan:", data.message);
    }
  } catch (error) {
    console.error(
      "Kesalahan jaringan mendapatkan status mengetik lawan:",
      error
    );
  }
}

// Polling pesan baru, status admin, dan status mengetik lawan
setInterval(async () => {
  if (chatPanel.classList.contains("active") && chatSessionId) {
    try {
      const response = await fetch(
        `./api/chat.php?action=get_new_messages&session_id=${chatSessionId}`
      );
      const data = await response.json();
      if (data.success) {
        data.new_messages.forEach((msg) => {
          appendMessage(msg.sender_type, msg.message_text, chatBody.id);
          if (isFullScreen) {
            appendMessage(
              msg.sender_type,
              msg.message_text,
              fullPageChatBody.id
            );
          }
        });
        if (data.new_messages.length > 0) {
          attachPromoCardListeners(); // Lampirkan ulang listener untuk pesan baru
          console.log(
            "Pesan baru diterima dan ditambahkan:",
            data.new_messages.length
          );
        }
      }
    } catch (error) {
      console.error("Kesalahan polling untuk pesan baru:", error);
    }
  }
  updateAdminStatus();
  getOpponentTypingStatus();
}, 1000);