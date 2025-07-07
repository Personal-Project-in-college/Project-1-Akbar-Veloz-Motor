<?php
include '../../../../helpers/functionCheckLogin.php';
checkLogin();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../auth/login.php');
    exit();
}

$admin_user_id = $_SESSION['user_id'];

$base_api_path = '../../../../api/';
?>

<?php
include '../layout/header.php';
?>

<link rel="stylesheet" href="./css/style.css">

<?php include '../layout/sidebar.php'; ?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="dashboard-header" id="dashboard-header">
            <h1>Manajemen Chat</h1>
            <p>Admin, <?php echo htmlspecialchars($_SESSION['name']); ?></p>
        </div>

        <div id="backButtonContainer" style="display: none; margin-bottom: 10px;">
            <button onclick="backToCustomerList()" style="padding: 8px 15px; background-color: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer;">&#x2190; Kembali ke Daftar Pelanggan</button>
        </div>

        <h3 id="daftar-chat-pelanggan">Daftar Chat Pelanggan</h3>
        <div class="customer-table-container" id="customerTableContainer" style="min-height: 20dvh;">
            <table class="customer-table" style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Customer</th>
                        <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Pesan Baru</th>
                        <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Status</th>
                    </tr>
                </thead>
                <tbody id="chatListTableBody">
                    <tr>
                        <td colspan="3" style="padding: 10px; border: 1px solid #ddd;">Memuat daftar chat...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="admin-chat-panel" id="adminChatPanel">
            <div class="no-chat-selected" id="noChatSelected">
                Pilih chat dari daftar di atas untuk memulai percakapan.
            </div>

            <div class="selectedChatView" id="selectedChatView" style="display: none; flex-direction: column; flex-grow: 1;">
                <div class="chat-header">
                    <div class="chat-header-left">
                        <img src="../../../../assets/images/profile-picture/image4.png" alt="Customer Avatar" style="width:40px; height:40px; border-radius:50%; margin-right:10px;" />
                        <div>
                            <h3 id="currentCustomerName" style="margin:0;"></h3>
                            <p style="margin:0;"><span id="currentChatStatus" class="user-status"></span></p>
                        </div>
                    </div>
                </div>
                <div class="chat-body" id="chatBody">
                </div>
                <div class="admin-options" id="promoButtons">
                    <button data-promo="about">Kirim: Apa itu Veloz?</button>
                    <button data-promo="promo">Kirim: Rekomendasi Kendaraan</button>
                    <button data-promo="budget">Kirim: Motor Budget</button>
                    <button id="sendAllPromosButton" class="send-all">Kirim Semua Promo</button>
                </div>
                <div class="chat-input">
                    <input
                        type="text"
                        placeholder="Ketik balasan Anda..."
                        id="adminChatInput" />
                    <button id="adminSendButton">
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
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>

<script>
    const ADMIN_USER_ID = <?php echo json_encode($admin_user_id); ?>;
    const BASE_API_PATH = <?php echo json_encode($base_api_path); ?>;
</script>

<script src="./js/script.js"></script>