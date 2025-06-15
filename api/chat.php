<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include_once 'db_connect.php';

require_once __DIR__ . '/../vendor/autoload.php';

use Ramsey\Uuid\Uuid;

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$action = $_GET['action'] ?? ($input['action'] ?? '');

function checkCustomerAuth($pdo)
{
    if (!isset($_SESSION['customer_id']) || empty($_SESSION['customer_id'])) {
        echo json_encode(['success' => false, 'message' => 'Pengguna tidak terautentikasi.', 'auth_failed' => true]);
        return false;
    }
    return true;
}

switch ($action) {
    case 'get_or_create_session':
        if (!checkCustomerAuth($pdo)) {
            exit();
        }

        $customer_id = $_SESSION['customer_id'];
        $session_id = $_SESSION['chat_session_id'] ?? null;
        $recent_session_threshold = date('Y-m-d H:i:s', strtotime('-24 hour'));

        if ($session_id) {
            $stmt = $pdo->prepare("SELECT cs.session_id FROM chat_sessions cs JOIN customers c ON cs.customer_id = c.id WHERE cs.session_id = ? AND cs.customer_id = ? AND cs.started_at >= ?");
            $stmt->execute([$session_id, $customer_id, $recent_session_threshold]);
            if ($stmt->fetch()) {
                $stmt_update_activity = $pdo->prepare("UPDATE chat_sessions SET status = 'pending', last_customer_activity = NOW(), customer_typing = FALSE WHERE session_id = ?");
                $stmt_update_activity->execute([$session_id]);
                echo json_encode(['success' => true, 'session_id' => $session_id]);
                exit();
            } else {
                unset($_SESSION['chat_session_id']);
                $session_id = null;
            }
        }

        $stmt_find_latest_relevant_session = $pdo->prepare("
            SELECT cs.session_id
            FROM chat_sessions cs
            JOIN customers c ON cs.customer_id = c.id
            WHERE cs.customer_id = ?
            AND cs.started_at >= ?
            ORDER BY cs.started_at DESC
            LIMIT 1
        ");
        $stmt_find_latest_relevant_session->execute([$customer_id, $recent_session_threshold]);
        $latest_relevant_session = $stmt_find_latest_relevant_session->fetch();

        if ($latest_relevant_session) {
            $session_id_to_use = $latest_relevant_session['session_id'];
            $stmt_reopen_session = $pdo->prepare("UPDATE chat_sessions SET status = 'pending', last_customer_activity = NOW(), customer_typing = FALSE WHERE session_id = ?");
            $stmt_reopen_session->execute([$session_id_to_use]);
            $_SESSION['chat_session_id'] = $session_id_to_use;
            echo json_encode(['success' => true, 'session_id' => $session_id_to_use]);
            exit();
        }

        $new_session_id = Uuid::uuid4()->toString();
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO chat_sessions (session_id, customer_id, status, started_at, last_customer_activity) VALUES (?, ?, 'pending', NOW(), NOW())");
            $stmt->execute([$new_session_id, $customer_id]);
            $_SESSION['chat_session_id'] = $new_session_id;
            $pdo->commit();
            echo json_encode(['success' => true, 'session_id' => $new_session_id]);
        } catch (\PDOException $e) {
            $pdo->rollBack();
            if ($e->getCode() == 23000) {
                echo json_encode(['success' => false, 'message' => 'Gagal membuat sesi chat: Duplikat ID sesi, coba lagi.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Kesalahan database: ' . $e->getMessage()]);
            }
        }
        break;

    case 'check_session':
        if (!checkCustomerAuth($pdo)) {
            exit();
        }
        $session_id_from_js = $input['session_id'] ?? '';
        $customer_id = $_SESSION['customer_id'];
        $recent_session_threshold = date('Y-m-d H:i:s', strtotime('-24 hour'));

        if (empty($session_id_from_js)) {
            echo json_encode(['success' => false, 'message' => 'ID sesi tidak boleh kosong.']);
            exit();
        }

        try {
            $stmt = $pdo->prepare("SELECT cs.session_id FROM chat_sessions cs JOIN customers c ON cs.customer_id = c.id WHERE cs.session_id = ? AND cs.customer_id = ? AND cs.started_at >= ?");
            $stmt->execute([$session_id_from_js, $customer_id, $recent_session_threshold]);
            if ($stmt->fetch()) {
                $stmt_update_activity = $pdo->prepare("UPDATE chat_sessions SET status = 'pending', last_customer_activity = NOW(), customer_typing = FALSE WHERE session_id = ?");
                $stmt_update_activity->execute([$session_id_from_js]);
                echo json_encode(['success' => true, 'message' => 'Sesi valid.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Sesi tidak ditemukan, sudah ditutup, atau terlalu lama.']);
            }
        } catch (\PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Kesalahan database: ' . $e->getMessage()]);
        }
        break;

    case 'send_message':
        $message_text = $input['message'] ?? '';
        $session_id = $input['session_id'] ?? '';
        $sender_type = $input['sender_type'] ?? '';

        if (empty($message_text) || empty($session_id) || empty($sender_type)) {
            echo json_encode(['success' => false, 'message' => 'Pesan, ID sesi, atau tipe pengirim tidak boleh kosong.']);
            exit();
        }

        $sender_id = null;
        $is_read_by_user = 0;
        $is_read_by_customer = 0;

        if ($sender_type === 'customer') {
            if (!checkCustomerAuth($pdo)) {
                exit();
            }
            $sender_id = $_SESSION['customer_id'];
            $is_read_by_user = 0;
            $is_read_by_customer = 1;

            $stmt_update_session = $pdo->prepare("UPDATE chat_sessions SET status = 'pending', last_customer_activity = NOW() WHERE session_id = ? AND status != 'closed'");
            $stmt_update_session->execute([$session_id]);
        } elseif ($sender_type === 'user') {
            $sender_id = $_SESSION['user_id'] ?? 0;
            $is_read_by_user = 1;
            $is_read_by_customer = 0;

            try {
                $stmt_update_session = $pdo->prepare("UPDATE chat_sessions SET status = 'open', user_id = ?, closed_at = NULL WHERE session_id = ? AND (status = 'pending' OR user_id IS NULL)");
                $stmt_update_session->execute([$sender_id, $session_id]);
            } catch (\PDOException $e) {
                // Ignore error if updating session fails for admin
            }
        } elseif ($sender_type === 'bot') {
            if (isset($_SESSION['customer_id'])) {
                $sender_id = $_SESSION['customer_id'];
                $is_read_by_customer = 1;
                $is_read_by_user = 0;
            } else if (isset($_SESSION['user_id'])) {
                $sender_id = $_SESSION['user_id'];
                $is_read_by_user = 1;
                $is_read_by_customer = 0;
            } else {
                $sender_id = 0;
                $is_read_by_user = 0;
                $is_read_by_customer = 0;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Tipe pengirim tidak valid atau tidak diizinkan.']);
            exit();
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO messages (chat_session_id, sender_id, sender_type, message_text, is_read_by_user, is_read_by_customer) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$session_id, $sender_id, $sender_type, $message_text, (int)$is_read_by_user, (int)$is_read_by_customer]);

            echo json_encode(['success' => true, 'message' => 'Pesan berhasil dikirim.']);
        } catch (\PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Gagal mengirim pesan: ' . $e->getMessage()]);
        }
        break;

    case 'get_history':
        $session_id = $_GET['session_id'] ?? '';
        if (empty($session_id)) {
            echo json_encode(['success' => false, 'message' => 'ID sesi tidak boleh kosong.']);
            exit();
        }

        if (isset($_SESSION['user_id'])) {
            try {
                $stmt_mark_read_user = $pdo->prepare("UPDATE messages SET is_read_by_user = TRUE WHERE chat_session_id = ? AND (sender_type = 'customer' OR sender_type = 'bot') AND is_read_by_user = FALSE");
                $stmt_mark_read_user->execute([$session_id]);
            } catch (\PDOException $e) {
                // Log error
            }
        } elseif (isset($_SESSION['customer_id'])) {
            try {
                $stmt_mark_read_customer = $pdo->prepare("UPDATE messages SET is_read_by_customer = TRUE WHERE chat_session_id = ? AND (sender_type = 'user' OR sender_type = 'bot') AND is_read_by_customer = FALSE");
                $stmt_mark_read_customer->execute([$session_id]);
                $stmt_update_activity = $pdo->prepare("UPDATE chat_sessions SET last_customer_activity = NOW() WHERE session_id = ?");
                $stmt_update_activity->execute([$session_id]);
            } catch (\PDOException $e) {
                // Log error
            }
        }

        try {
            $stmt = $pdo->prepare("SELECT sender_type, message_text, timestamp FROM messages WHERE chat_session_id = ? ORDER BY timestamp ASC");
            $stmt->execute([$session_id]);
            $messages = $stmt->fetchAll();

            echo json_encode(['success' => true, 'messages' => $messages]);
        } catch (\PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Gagal mengambil riwayat chat: ' . $e->getMessage()]);
        }
        break;

    case 'get_new_messages':
        if (!checkCustomerAuth($pdo)) {
            exit();
        }
        $session_id = $_GET['session_id'] ?? '';

        if (empty($session_id)) {
            echo json_encode(['success' => false, 'message' => 'ID sesi tidak boleh kosong.']);
            exit();
        }

        try {
            $stmt_update_activity = $pdo->prepare("UPDATE chat_sessions SET last_customer_activity = NOW() WHERE session_id = ?");
            $stmt_update_activity->execute([$session_id]);

            $stmt = $pdo->prepare("SELECT id, sender_type, message_text, timestamp FROM messages WHERE chat_session_id = ? AND (sender_type = 'user' OR sender_type = 'bot') AND is_read_by_customer = FALSE ORDER BY timestamp ASC");
            $stmt->execute([$session_id]);
            $new_messages = $stmt->fetchAll();

            if (!empty($new_messages)) {
                $message_ids = array_column($new_messages, 'id');
                $placeholders = implode(',', array_fill(0, count($message_ids), '?'));
                $stmt_mark_read = $pdo->prepare("UPDATE messages SET is_read_by_customer = TRUE WHERE id IN ($placeholders)");
                $stmt_mark_read->execute($message_ids);
            }

            echo json_encode(['success' => true, 'new_messages' => $new_messages]);
        } catch (\PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Gagal mengambil pesan baru: ' . $e->getMessage()]);
        }
        break;

    case 'get_new_messages_admin':
        $session_id = $_GET['session_id'] ?? '';

        if (empty($session_id)) {
            echo json_encode(['success' => false, 'message' => 'ID sesi tidak boleh kosong.']);
            exit();
        }

        try {
            $stmt = $pdo->prepare("SELECT id, sender_type, message_text, timestamp FROM messages WHERE chat_session_id = ? AND (sender_type = 'customer' OR sender_type = 'bot') AND is_read_by_user = FALSE ORDER BY timestamp ASC");
            $stmt->execute([$session_id]);
            $new_messages = $stmt->fetchAll();

            echo json_encode(['success' => true, 'new_messages' => $new_messages]);
        } catch (\PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Gagal mengambil pesan baru untuk admin: ' . $e->getMessage()]);
        }
        break;

    case 'mark_as_read_admin':
        $session_id = $input['session_id'] ?? '';
        if (empty($session_id)) {
            echo json_encode(['success' => false, 'message' => 'ID sesi tidak boleh kosong.']);
            exit();
        }
        try {
            $stmt = $pdo->prepare("UPDATE messages SET is_read_by_user = TRUE WHERE chat_session_id = ? AND (sender_type = 'customer' OR sender_type = 'bot') AND is_read_by_user = FALSE");
            $stmt->execute([$session_id]);
            echo json_encode(['success' => true, 'message' => 'Pesan ditandai sudah dibaca oleh admin.']);
        } catch (\PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Gagal menandai pesan sudah dibaca: ' . $e->getMessage()]);
        }
        break;

    case 'get_chat_sessions':
        try {
            $customer_activity_threshold_seconds = 60;
            $session_display_threshold = date('Y-m-d H:i:s', strtotime('-24 hour'));

            $stmt = $pdo->prepare("
                WITH RankedSessions AS (
                    SELECT
                        cs.customer_id,
                        cs.session_id,
                        cs.status,
                        cs.started_at,
                        cs.last_customer_activity,
                        cs.customer_typing,
                        ROW_NUMBER() OVER(PARTITION BY cs.customer_id ORDER BY cs.started_at DESC) as rn
                    FROM
                        chat_sessions cs
                    WHERE
                        cs.started_at >= :session_display_threshold
                )
                SELECT
                    c.id AS customer_id,
                    c.email AS customer_email,
                    c.is_logged_in,
                    rs.session_id AS latest_session_id,
                    rs.status AS latest_session_status,
                    rs.started_at AS latest_session_start,
                    rs.last_customer_activity AS latest_customer_activity,
                    rs.customer_typing AS customer_typing,
                    (
                        SELECT COUNT(m.id)
                        FROM messages m
                        WHERE m.chat_session_id = rs.session_id AND (m.sender_type = 'customer' OR m.sender_type = 'bot') AND m.is_read_by_user = FALSE
                    ) AS unread_messages
                FROM
                    customers c
                JOIN
                    RankedSessions rs ON c.id = rs.customer_id AND rs.rn = 1
                ORDER BY
                    unread_messages DESC, latest_session_start DESC
            ");
            $stmt->execute([':session_display_threshold' => $session_display_threshold]);
            $sessions = $stmt->fetchAll();

            $formatted_sessions = [];
            foreach ($sessions as $session) {
                $customer_status_text = 'Offline';

                if ($session['customer_typing'] == 1) {
                    $customer_status_text = 'Mengetik...';
                } else if ($session['is_logged_in'] == 1 && strtotime($session['latest_customer_activity'] ?? '0000-00-00 00:00:00') >= (time() - $customer_activity_threshold_seconds)) {
                    $customer_status_text = 'Online';
                } else if ($session['is_logged_in'] == 1) {
                    $customer_status_text = 'Idle';
                } else if ($session['is_logged_in'] == 0) {
                    $customer_status_text = 'Logout';
                }

                $formatted_sessions[] = [
                    'customer_id' => $session['customer_id'],
                    'customer_email' => $session['customer_email'],
                    'unread_messages' => $session['unread_messages'],
                    'latest_session_id' => $session['latest_session_id'],
                    'latest_session_status' => $session['latest_session_status'],
                    'customer_online_status' => $customer_status_text
                ];
            }

            echo json_encode(['success' => true, 'sessions' => $formatted_sessions]);
        } catch (\PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Gagal mengambil sesi chat: ' . $e->getMessage()]);
        }
        break;

    case 'check_admin_availability':
        try {
            $activity_threshold_seconds = 60;
            $activity_threshold_time = date('Y-m-d H:i:s', time() - $activity_threshold_seconds);

            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE is_online = TRUE AND last_activity >= ?");
            $stmt->execute([$activity_threshold_time]);
            $online_users_count = $stmt->fetchColumn();

            echo json_encode(['success' => true, 'users_available' => $online_users_count > 0]);
        } catch (\PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Gagal cek ketersediaan admin: ' . $e->getMessage()]);
        }
        break;

    case 'update_typing_status':
        $session_id = $input['session_id'] ?? '';
        $is_typing = $input['is_typing'] ?? false;
        $who_typing = $input['who'] ?? '';

        if (empty($session_id) || empty($who_typing)) {
            echo json_encode(['success' => false, 'message' => 'ID Sesi dan pengirim tidak boleh kosong.']);
            exit();
        }

        if ($who_typing === 'customer') {
            if (!checkCustomerAuth($pdo)) {
                exit();
            }
        } elseif ($who_typing !== 'user') {
            echo json_encode(['success' => false, 'message' => 'Tipe pengirim tidak valid atau tidak diizinkan.']);
            exit();
        }

        try {
            $typing_status_column = ($who_typing === 'customer') ? 'customer_typing' : 'user_typing';
            $stmt = $pdo->prepare("UPDATE chat_sessions SET {$typing_status_column} = ? WHERE session_id = ?");
            $stmt->execute([(int)$is_typing, $session_id]);
            echo json_encode(['success' => true]);
        } catch (\PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Gagal update status mengetik: ' . $e->getMessage()]);
        }
        break;

    case 'get_typing_status':
        $session_id = $_GET['session_id'] ?? '';
        $who_checking = $_GET['who'] ?? '';

        if (empty($session_id) || empty($who_checking)) {
            echo json_encode(['success' => false, 'message' => 'ID Sesi dan pemeriksa tidak boleh kosong.']);
            exit();
        }

        try {
            $opponent_typing_column = ($who_checking === 'customer') ? 'user_typing' : 'customer_typing';
            $stmt = $pdo->prepare("SELECT {$opponent_typing_column} FROM chat_sessions WHERE session_id = ?");
            $stmt->execute([$session_id]);
            $result = $stmt->fetch();

            $is_opponent_typing = (bool)($result[$opponent_typing_column] ?? false);

            echo json_encode(['success' => true, 'is_typing' => $is_opponent_typing]);
        } catch (\PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Gagal mendapatkan status mengetik: ' . $e->getMessage()]);
        }
        break;

    case 'send_specific_promo':
        $sessionId = $input['session_id'] ?? null;
        $promoTarget = $input['promo_target'] ?? null;

        if (!$sessionId || !$promoTarget) {
            echo json_encode(['success' => false, 'message' => 'Session ID atau promo target hilang.']);
            exit();
        }

        $promoContent = '';
        switch ($promoTarget) {
            case 'about':
                $promoContent = '
                    <strong>Lebih dekat dengan Veloz!</strong>
                    <div class="promo-card" data-target="about">
                        <h4>Apa itu Akbar Veloz Motor?</h4>
                        <p>Temukan semua informasi tentang produk kami</p>
                    </div>';
                break;
            case 'promo':
                $promoContent = '
                    <strong>Yuk intip promo seru!</strong>
                    <div class="promo-card" data-target="promo">
                        <h4>Rekomendasi Kendaraan Akhir Tahun</h4>
                        <p>Lihat promo khusus akhir tahun</p>
                    </div>';
                break;
            case 'budget':
                $promoContent = '
                    <strong>Cari motor sesuai budget?</strong>
                    <div class="promo-card" data-target="budget">
                        <h4>Motor dengan Budget Murah!</h4>
                        <p>Temukan motor sesuai budget Anda</p>
                    </div>';
                break;
            default:
                echo json_encode(['success' => false, 'message' => 'Target promo tidak valid.']);
                exit();
        }

        $sender_id = $_SESSION['user_id'] ?? 0;
        $sender_type = 'user';
        $is_read_by_user = 1;
        $is_read_by_customer = 0;

        try {
            $stmt = $pdo->prepare("INSERT INTO messages (chat_session_id, sender_id, sender_type, message_text, is_read_by_user, is_read_by_customer) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$sessionId, $sender_id, $sender_type, $promoContent, $is_read_by_user, $is_read_by_customer]);
            echo json_encode(['success' => true, 'message' => 'Promo berhasil dikirim.']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Kesalahan database saat mengirim promo spesifik: ' . $e->getMessage()]);
        }
        break;

    case 'send_all_promos':
        $sessionId = $input['session_id'] ?? null;

        if (!$sessionId) {
            echo json_encode(['success' => false, 'message' => 'Session ID hilang.']);
            exit();
        }

        $allPromoContent = '
            <strong>Lihat semua yang kami tawarkan!</strong>
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
            </div>';

        $sender_id = $_SESSION['user_id'] ?? 0;
        $sender_type = 'user';
        $is_read_by_user = 1;
        $is_read_by_customer = 0;

        try {
            $stmt = $pdo->prepare("INSERT INTO messages (chat_session_id, sender_id, sender_type, message_text, is_read_by_user, is_read_by_customer) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$sessionId, $sender_id, $sender_type, $allPromoContent, $is_read_by_user, $is_read_by_customer]);
            echo json_encode(['success' => true, 'message' => 'Semua promo berhasil dikirim.']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Kesalahan database saat mengirim semua promo: ' . $e->getMessage()]);
        }
        break;

    case 'get_vehicles':
        try {
            $stmt = $pdo->query("
            SELECT
                v.id AS vehicle_id,
                vm.name AS model_name,
                b.name AS brand_name,
                v.price_displayed AS price,
                v.lowest_price AS min_price,
                (SELECT photo_path FROM vehicle_photos WHERE vehicle_id = v.id ORDER BY id ASC LIMIT 1) AS image,
                v.description
            FROM
                vehicles v
            JOIN
                vehicle_models vm ON v.vehicle_model_id = vm.id
            JOIN
                brands b ON vm.brand_id = b.id
            WHERE
                v.deleted_at IS NULL
                AND vm.deleted_at IS NULL
                AND b.deleted_at IS NULL
                AND v.status = 'available'
                AND v.stnk_deadline >= CURDATE()
            ORDER BY
                b.name ASC, vm.name ASC
        ");
            $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $base_image_url = 'http://localhost:8888/project-galacticos-v-2.0/public/uploads/';

            foreach ($vehicles as &$vehicle) {
                $vehicle['display_name'] = $vehicle['vehicle_id'] . ' - ' . $vehicle['brand_name'] . ' ' . $vehicle['model_name'];

                if (!empty($vehicle['image'])) {
                    $vehicle['image'] = $base_image_url . $vehicle['image'];
                }
            }
            unset($vehicle);

            echo json_encode(['success' => true, 'vehicles' => $vehicles]);
        } catch (\PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Gagal mengambil data kendaraan: ' . $e->getMessage()]);
        }
        break;

    case 'submit_negotiation_offer':
        if (!checkCustomerAuth($pdo)) {
            exit();
        }

        $session_id = $input['session_id'] ?? '';
        $vehicle_id = $input['vehicle_id'] ?? null;
        $offer_amount = $input['offer_amount'] ?? null;
        $customer_message_text = $input['customer_message_text'] ?? '';

        $missing_fields = [];
        if (empty($session_id)) $missing_fields[] = 'session_id';
        if (empty($vehicle_id)) $missing_fields[] = 'vehicle_id';
        if (!isset($offer_amount) || !is_numeric($offer_amount) || $offer_amount < 0) $missing_fields[] = 'offer_amount (invalid/missing)';
        if (empty($customer_message_text)) $missing_fields[] = 'customer_message_text';

        if (!empty($missing_fields)) {
            echo json_encode(['success' => false, 'message' => 'Gagal memproses penawaran: Data negosiasi tidak lengkap.']);
            exit();
        }

        try {
            $stmt_vehicle = $pdo->prepare("
                SELECT
                    v.price_displayed,
                    v.lowest_price,
                    vm.name AS vehicle_model_name,
                    b.name AS brand_name,
                    v.id AS vehicle_plate_id
                FROM
                    vehicles v
                JOIN
                    vehicle_models vm ON v.vehicle_model_id = vm.id
                JOIN
                    brands b ON vm.brand_id = b.id
                WHERE
                    v.id = ?
            ");
            $stmt_vehicle->execute([$vehicle_id]);
            $vehicle = $stmt_vehicle->fetch(PDO::FETCH_ASSOC);

            if (!$vehicle) {
                echo json_encode(['success' => false, 'message' => 'Kendaraan tidak ditemukan.']);
                exit();
            }

            $actual_price = $vehicle['price_displayed'];
            $min_acceptable_price = $vehicle['lowest_price'];

            $customer_id = $_SESSION['customer_id'];

            $stmt_insert_customer_message = $pdo->prepare("INSERT INTO messages (chat_session_id, sender_id, sender_type, message_text, is_read_by_user, is_read_by_customer) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt_insert_customer_message->execute([$session_id, $customer_id, 'customer', $customer_message_text, 0, 1]);


            $response_message_customer_html = '';
            $response_message_admin_text = '';
            $full_vehicle_name = $vehicle['vehicle_plate_id'] . ' - ' . htmlspecialchars($vehicle['brand_name']) . ' ' . htmlspecialchars($vehicle['vehicle_model_name']);

            if ($offer_amount >= $min_acceptable_price) {
              $response_message_customer_html = '<strong>Penawaran DITERIMA!</strong><p>Penawaran Anda Rp' . number_format($offer_amount, 0, ',', '.') . ' untuk ' . $full_vehicle_name . ' diterima.</p><button class="negotiation-btn" data-action="testDrive" data-vehicle-id="' . htmlspecialchars($vehicle_id) . '" data-negotiated-price="' . htmlspecialchars($offer_amount) . '">Lanjut Test Drive</button><button class="negotiation-btn" data-action="continueTransaction" data-vehicle-id="' . htmlspecialchars($vehicle_id) . '" data-negotiated-price="' . htmlspecialchars($offer_amount) . '">Lanjut Transaksi</button>';
    $response_message_admin_text = 'Penawaran DITERIMA untuk ' . $full_vehicle_name . '. Nominal: Rp' . number_format($offer_amount, 0, ',', '.') . '. Pelanggan diminta Klik "Lanjut Transaksi" atau "Lanjut Test Drive".';
            } else {
                $response_message_customer_html = '<strong>Penawaran DITOLAK</strong><p>Maaf, penawaran Rp' . number_format($offer_amount, 0, ',', '.') . ' untuk ' . $full_vehicle_name . ' terlalu rendah.</p><button class="negotiation-btn" data-action="tryAgain">Coba Lagi</button><button class="negotiation-btn" data-action="selectOtherVehicle">Pilih Kendaraan Lain</button>';
                $response_message_admin_text = 'Penawaran DITOLAK untuk ' . $full_vehicle_name . '. Nominal: Rp' . number_format($offer_amount, 0, ',', '.') . '. Terlalu rendah.';
            }

            $stmt_insert_bot_message_for_customer = $pdo->prepare("INSERT INTO messages (chat_session_id, sender_id, sender_type, message_text, is_read_by_user, is_read_by_customer) VALUES (?, ?, 'bot', ?, ?, ?)");
            $stmt_insert_bot_message_for_customer->execute([$session_id, $customer_id, $response_message_customer_html, 0, 1]);

            $stmt_insert_bot_message_for_admin = $pdo->prepare("INSERT INTO messages (chat_session_id, sender_id, sender_type, message_text, is_read_by_user, is_read_by_customer) VALUES (?, ?, 'bot', ?, ?, ?)");
            $stmt_insert_bot_message_for_admin->execute([$session_id, $customer_id, $response_message_admin_text, 0, 0]);


            echo json_encode([
                'success' => true,
                'message' => 'Penawaran berhasil diajukan.',
                'customer_bot_response_html' => $response_message_customer_html
            ]);
        } catch (\PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Gagal mengajukan penawaran: ' . $e->getMessage()]);
        }
        break;

    case 'create_test_drive_order':
        if (!checkCustomerAuth($pdo)) {
            exit();
        }

        $customer_id = $_SESSION['customer_id'];
        $vehicle_id = $input['vehicle_id'] ?? null;
        $negotiated_price = $input['negotiated_price'] ?? 0.00;

        if (empty($vehicle_id)) {
            echo json_encode(['success' => false, 'message' => 'ID kendaraan tidak boleh kosong.']);
            exit();
        }

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO orders (customer_id, vehicle_id, negotiated_price, type_order, status, is_read, created_at) VALUES (?, ?, ?, 'test_driver', 'proced', 0, NOW())");
            $stmt->execute([$customer_id, $vehicle_id, $negotiated_price]);
            $order_id = $pdo->lastInsertId();

            $stmt_update_vehicle_status = $pdo->prepare("UPDATE vehicles SET status = 'test_drive' WHERE id = ?");
            $stmt_update_vehicle_status->execute([$vehicle_id]);

            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Order Lanjut Test Drive berhasil dibuat.', 'order_id' => $order_id]);
        } catch (\PDOException $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Gagal membuat order Lanjut Test Drive: ' . $e->getMessage()]);
        }
        break;

    case 'create_transaction_order':
        if (!checkCustomerAuth($pdo)) {
            exit();
        }

        $customer_id = $_SESSION['customer_id'];
        $vehicle_id = $input['vehicle_id'] ?? null;
        $negotiated_price = $input['negotiated_price'] ?? 0.00;

        if (empty($vehicle_id) || !isset($negotiated_price) || !is_numeric($negotiated_price) || $negotiated_price < 0) {
            echo json_encode(['success' => false, 'message' => 'Data transaksi tidak lengkap atau tidak valid.']);
            exit();
        }

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO orders (customer_id, vehicle_id, negotiated_price, type_order, status, is_read, created_at) VALUES (?, ?, ?, 'transaction', 'proced', 0, NOW())");
            $stmt->execute([$customer_id, $vehicle_id, $negotiated_price]);
            $order_id = $pdo->lastInsertId();

            // Opsional: Perbarui status kendaraan menjadi 'transaction' atau 'on_loan' jika sudah dibayar sebagian/lunas
            // Anda mungkin ingin logika ini lebih kompleks di masa depan (misal: hanya update setelah pembayaran berhasil)
            $stmt_update_vehicle_status = $pdo->prepare("UPDATE vehicles SET status = 'transaction' WHERE id = ?");
            $stmt_update_vehicle_status->execute([$vehicle_id]);

            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Order Transaksi berhasil dibuat.', 'order_id' => $order_id]);
        } catch (\PDOException $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Gagal membuat order Transaksi: ' . $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Aksi tidak valid.']);
        break;
}