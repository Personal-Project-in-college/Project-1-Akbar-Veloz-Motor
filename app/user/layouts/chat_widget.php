<style>
    /* ketika admin diskonek maka akan mengirim pesan ini, Terima kasih atas pesan Anda! tunggu di jawab oleh admin jika online tidak mengirim pesan teresbut */
    
    :root {
        --error-color: #ff4444;
        --success-color: #00c851;
        --chat-bg: #ece5dd;
        --user-msg: #dcf8c6;
        --bot-msg: #ffffff;
    }

    body {
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 0;
    }

    /* Floating Chat Button */
    .chat-button {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        background-color: var(--secondary-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        cursor: pointer;
        transition: all 0.3s ease;
        z-index: 1000;
    }

    .chat-button:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
    }

    .chat-button svg {
        width: 30px;
        fill: var(--primary-color);
        height: 30px;
    }

    /* Chat Panel Styles */
    .chat-panel {
        position: fixed;
        bottom: 100px;
        right: 30px;
        width: 350px;
        height: 500px;
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        display: none;
        flex-direction: column;
        overflow: hidden;
        z-index: 1000;
        transition: all 0.4s ease-in-out;
    }

    .chat-panel.fullscreen {
        transform: scale(1);
        transition: all 0.4s ease-in-out, transform 0.4s ease-in-out;
    }

    .chat-panel.active {
        display: flex;
    }

    .chat-panel.fullscreen {
        width: 100%;
        height: 100vh;
        bottom: 0;
        right: 0;
        border-radius: 0;
    }

    /* Chat Header */
    .chat-header {
        background-color: var(--primary-color);
        color: white;
        padding: 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: move;
        user-select: none;
    }

    .chat-header-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .chat-header img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }

    .chat-header h3 {
        margin: 0;
        font-size: 16px;
    }

    .chat-header p {
        margin: 0;
        font-size: 12px;
        opacity: 0.8;
    }

    .chat-actions {
        display: flex;
        gap: 10px;
    }

    .vehicle-card-chat img{
        border-radius: 8px;

    }

    .chat-action-btn {
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        font-size: 16px;
    }

    /* Chat Body */
    .chat-body {
        flex: 1;
        padding: 15px;
        overflow-y: auto;
        background-color: var(--chat-bg);
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .message {
        max-width: 80%;
        padding: 10px 15px;
        border-radius: 18px;
        font-size: 14px;
        line-height: 1.4;
        position: relative;
    }

    .bot-message {
        background-color: var(--bot-msg);
        align-self: flex-start;
        border-bottom-left-radius: 4px;
    }

    .user-message {
        background-color: var(--user-msg);
        align-self: flex-end;
        border-bottom-right-radius: 4px;
    }

    .promo-card {
        background-color: white;
        border-radius: 12px;
        padding: 12px;
        margin: 8px 0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        transition: transform 0.2s;
    }

    .promo-card:hover {
        transform: translateY(-2px);
    }

    .promo-card h4 {
        margin: 0 0 8px 0;
        color: var(--primary-color);
    }

    .promo-card p {
        margin: 0;
        font-size: 12px;
        color: #555;
    }

    /* Chat Input */
    .chat-input {
        padding: 12px;
        background-color: white;
        display: flex;
        gap: 8px;
        border-top: 1px solid #eee;
    }

    .chat-input input {
        flex: 1;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 24px;
        outline: none;
        font-size: 14px;
    }

    .chat-input button {
        background-color: var(--primary-color);
        color: white;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    /* Full Page Chat (hidden by default) */
    .full-page-chat {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100vh;
        background-color: white;
        z-index: 2000;
        display: none;
        flex-direction: column;
    }

    .full-page-chat.active {
        display: flex;
    }

    .full-page-header {
        padding: 15px;
        background-color: var(--primary-color);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .back-button {
        background: none;
        border: none;
        color: white;
        font-size: 16px;
        cursor: pointer;
    }

    /* Negotiation Specific Styles */
    .vehicle-card-chat {
        background: white;
        border-radius: 12px;
        padding: 12px;
        margin: 8px 0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .description-card{
        font-weight: normal;
        color: black;
     }


    .vehicle-option {
        cursor: pointer;
        transition: transform 0.2s;
        background-color: white;
        border-radius: 12px;
        padding: 12px;
        margin: 8px 0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .vehicle-option:hover {
        transform: translateY(-2px);
    }

    .offer-input {
        display: flex;
        gap: 8px;
        margin-top: 10px;
    }

    .offer-input input {
        flex: 1;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .offer-input button {
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 4px;
        padding: 0 15px;
        cursor: pointer;
    }

    .negotiation-btn {
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 4px;
        padding: 8px 15px;
        margin: 5px;
        cursor: pointer;
        font-size: 14px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .chat-panel {
            width: 90%;
            right: 5%;
        }

        .chat-panel.fullscreen {
            width: 100%;
            right: 0;
        }
    }
</style>

<body>
    <!-- Floating Chat Button -->
    <div class="chat-button" id="chatButton">
        <svg fill="#000000" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 458 458" xml:space="preserve">
            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
            <g id="SVGRepo_iconCarrier">
                <g>
                    <g>
                        <path d="M428,41.534H30c-16.569,0-30,13.431-30,30v252c0,16.568,13.432,30,30,30h132.1l43.942,52.243 c5.7,6.777,14.103,10.69,22.959,10.69c8.856,0,17.258-3.912,22.959-10.69l43.942-52.243H428c16.568,0,30-13.432,30-30v-252 C458,54.965,444.568,41.534,428,41.534z M323.916,281.534H82.854c-8.284,0-15-6.716-15-15s6.716-15,15-15h241.062 c8.284,0,15,6.716,15,15S332.2,281.534,323.916,281.534z M67.854,198.755c0-8.284,6.716-15,15-15h185.103c8.284,0,15,6.716,15,15 s-6.716,15-15,15H82.854C74.57,213.755,67.854,207.039,67.854,198.755z M375.146,145.974H82.854c-8.284,0-15-6.716-15-15 s6.716-15,15-15h292.291c8.284,0,15,6.716,15,15C390.146,139.258,383.43,145.974,375.146,145.974z"></path>
                    </g>
                </g>
            </g>
        </svg>
    </div>

    <!-- Floating Chat Panel -->
    <div class="chat-panel" id="chatPanel">
        <div class="chat-header" id="chatHeader">
            <div class="chat-header-left">
                <img src="./assets/images/profile-picture/the-winner.jpeg" alt="SiVeloz" />
                <div>
                    <h3>SiVeloz</h3>
                    <p>Your Virtual Assistant</p>
                </div>
            </div>
            <div class="chat-actions">
                <button class="chat-action-btn" id="fullscreenBtn">⛶</button>
                <button class="chat-action-btn" id="expandBtn">↗</button>
                <button class="chat-action-btn" id="closeBtn">×</button>
            </div>
        </div>
        <div class="chat-body" id="chatBody">
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
        </div>
        <div class="chat-input">
            <input
                type="text"
                placeholder="Punya ide baru, atau saran? Ketik disini!"
                id="chatInput" />
            <button id="sendButton">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="20"
                    height="20"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round">
                    <line x1="22" y1="2" x2="11" y2="13"></line>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                </svg>
            </button>
        </div>
    </div>

    <!-- Full Page Chat -->
    <div class="full-page-chat" id="fullPageChat">
        <div class="full-page-header">
            <button class="back-button" id="backButton">← Kembali</button>
            <h3>SiVeloz - Chat Layanan</h3>
            <div style="width: 40px"></div>
        </div>
        <div class="chat-body" id="fullPageChatBody"></div>
        <div class="chat-input">
            <input
                type="text"
                placeholder="Ketik pesan Anda..."
                id="fullPageInput" />
            <button id="fullPageSendButton">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="20"
                    height="20"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round">
                    <line x1="22" y1="2" x2="11" y2="13"></line>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                </svg>
            </button>
        </div>
    </div>

  