console.log("koneksi chat aktive");

async function requestNotificationPermission() {
  if (!("Notification" in window)) {
    console.warn("Browser ini tidak mendukung notifikasi desktop.");
    return false;
  }

  if (Notification.permission === "granted") {
    console.log("Izin notifikasi sudah diberikan.");
    return true;
  }

  if (Notification.permission === "denied") {
    console.warn("Izin notifikasi ditolak oleh pengguna.");
    return false;
  }

  try {
    const permission = await Notification.requestPermission();
    if (permission === "granted") {
      console.log("Izin notifikasi diberikan.");
      return true;
    } else {
      console.warn("Izin notifikasi tidak diberikan.");
      return false;
    }
  } catch (error) {
    console.error("Kesalahan saat meminta izin notifikasi:", error);
    return false;
  }
}

function showChatNotification(title, body) {
  if (Notification.permission === "granted") {
    if (!chatPanel.classList.contains("active")) {
      new Notification(title, {
        body: body,
        icon: "./assets/images/profile-picture/the-winner.jpeg",
      });
    }
  } else {
    console.warn("Tidak dapat menampilkan notifikasi: Izin tidak diberikan.");
  }
}

const displayedMessageIds = new Set();

setInterval(async () => {
  if (chatSessionId) {
    try {
      // Kirim status apakah chat panel sedang aktif
      const isChatPanelCurrentlyActive = chatPanel.classList.contains("active");
      const response = await fetch(
        `./api/chat.php?action=get_new_messages&session_id=${chatSessionId}&is_chat_active=${isChatPanelCurrentlyActive}`
      );
      const data = await response.json();

      if (data.success) {
        data.new_messages.forEach((msg) => {
          if (!displayedMessageIds.has(msg.id)) {
            appendMessage(msg.sender_type, msg.message_text, chatBody.id);
            if (isFullScreen) {
              appendMessage(
                msg.sender_type,
                msg.message_text,
                fullPageChatBody.id
              );
            }
            displayedMessageIds.add(msg.id);

            // Tampilkan notifikasi desktop hanya jika chat TIDAK aktif
            showChatNotification("Pesan Baru dari SiVeloz", msg.message_text);
          }
        });
        if (data.new_messages.length > 0) {
          attachPromoCardListeners();
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
  await updateUnreadMessageCount(); // Selalu perbarui badge
}, 1000);

document.addEventListener("DOMContentLoaded", async () => {
  await getOrCreateChatSession();
  await fetchVehiclesChat();
  await loadChatHistory(); // Memuat riwayat dan mengisi `displayedMessageIds`
  await updateAdminStatus();
  await requestNotificationPermission();
  await updateUnreadMessageCount(); // Panggil ini sekali di awal untuk status badge yang benar
  console.log("Halaman dimuat, inisialisasi selesai.");
});


async function fetchVehiclesChat() {
  try {
    const response = await fetch("./api/chat.php?action=get_vehicles");
    const data = await response.json();
    if (data.success) {
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

async function sendChatMessageToBackend(message, senderType) {
  try {
    const response = await fetch("./api/chat.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        action: "send_message",
        message: message,
        session_id: chatSessionId,
        sender_type: senderType,
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

async function sendTypingStatus(isTyping) {
  if (!chatSessionId) return;

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
          who: "customer",
        }),
      });
      console.log("Status mengetik customer dikirim:", isTyping);
    } catch (error) {
      console.error("Kesalahan saat mengirim status mengetik customer:", error);
    }
  }
}

async function getOrCreateChatSession() {
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
        chatSessionId = null;
        console.warn(
          "Sesi chat yang ada tidak valid atau ditutup, membuat yang baru."
        );
      } else {
        console.log("Sesi chat dari PHP valid dan aktif.");
      }
    } catch (error) {
      console.error("Kesalahan memeriksa sesi yang ada:", error);
      chatSessionId = null;
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
      displayedMessageIds.clear();

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

      attachPromoCardListeners();

      data.messages.forEach((msg) => {
        const staticMessages = [
            "Halo! Butuh bantuan?",
            "Ngobrol dengan kami sekarang!",
            "Berbicara dengan chatbot agent"
        ];
        const isStaticBotMessage = staticMessages.includes(msg.message_text.trim());

        const isPromoCardBotResponse = msg.sender_type === "bot" && msg.message_text.includes("promo-card");

        const isNegotiationResponse =
          msg.message_text.includes("Penawaran DITERIMA!") ||
          msg.message_text.includes("Penawaran DITOLAK") ||
          msg.message_text.includes("Pilih kendaraan untuk dinegosiasikan:") ||
          msg.message_text.includes("Silakan masukkan penawaran baru:") ||
          msg.message_text.includes("Memulai negosiasi baru...");

        if (
            (!isStaticBotMessage && !isPromoCardBotResponse) ||
            isNegotiationResponse ||
            msg.sender_type === "customer" ||
            msg.sender_type === "user"
        ) {
          if (msg.id) {
              displayedMessageIds.add(msg.id);
          }
          appendMessage(msg.sender_type, msg.message_text, chatBody.id);
          if (isFullScreen) {
            appendMessage(
              msg.sender_type,
              msg.message_text,
              fullPageChatBody.id
            );
          }
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

async function updateAdminStatus() {
  try {
    const response = await fetch(
      "./api/chat.php?action=check_admin_availability"
    );
    const data = await response.json();
    if (data.success) {
      adminIsOnline = data.users_available;
      if (adminIsOnline) {
        adminStatusElement.textContent = "Online";
        adminStatusElement.classList.remove("offline");
        adminStatusElement.classList.add("online");
      } else {
        adminStatusElement.textContent = "Offline";
        adminStatusElement.classList.remove("online");
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

async function getOpponentTypingStatus() {
  if (!chatSessionId || !chatPanel.classList.contains("active")) return;

  try {
    const response = await fetch(
      `./api/chat.php?action=get_typing_status&session_id=${chatSessionId}&who=customer`
    );
    const data = await response.json();
    if (data.success) {
      if (data.is_typing) {
        adminStatusElement.textContent = "Mengetik...";
        adminStatusElement.classList.add("typing");
        adminStatusElement.classList.remove("offline", "online");
        addTypingIndicatorBubble(chatBody.id, "user");
        if (isFullScreen)
          addTypingIndicatorBubble(fullPageChatBody.id, "user");
      } else {
        removeTypingIndicatorBubble(chatBody.id);
        if (isFullScreen) removeTypingIndicatorBubble(fullPageChatBody.id);
        updateAdminStatus();
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