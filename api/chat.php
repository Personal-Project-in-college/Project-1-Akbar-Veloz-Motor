<?php
$env = parse_ini_file(__DIR__ . '/../.env');

$DOMAIN = $env['BASE_DOMAIN'];

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

function callGeminiAPI($apiKey, $prompt)
{
    // $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $apiKey;
    $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $apiKey;

    $data = [
        'contents' => [
            [
                'parts' => [
                    [
                        'text' => $prompt
                    ]
                ]
            ]
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode != 200) {
        return null;
    }

    $result = json_decode($response, true);

    if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        return $result['candidates'][0]['content']['parts'][0]['text'];
    }

    return null;
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
            $stmt = $pdo->prepare("SELECT cs.session_id FROM chat_sessions cs JOIN customers c ON cs.session_id = ? AND cs.customer_id = ? AND cs.started_at >= ?");
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
            // Handle exception if needed
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Tipe pengirim tidak valid.']);
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO messages (chat_session_id, sender_id, sender_type, message_text, is_read_by_user, is_read_by_customer) VALUES (?, ?, ?, ?, ?, ?)");
        // Menggunakan htmlspecialchars untuk pesan yang dikirim pengguna agar aman disimpan
        $stmt->execute([$session_id, $sender_id, $sender_type, htmlspecialchars($message_text), (int)$is_read_by_user, (int)$is_read_by_customer]);

        $ai_response = null;
        $ai_responses = [];

        if ($sender_type === 'customer') {

            if (trim($message_text) === 'Motor dengan Budget Murah!') {
                echo json_encode(['success' => true, 'message' => 'Pesan pemicu negosiasi diterima.']);
                exit();
            }

            $activity_threshold_seconds = 60;
            $activity_threshold_time = date('Y-m-d H:i:s', time() - $activity_threshold_seconds);
            $stmt_admin = $pdo->prepare("SELECT COUNT(*) FROM users WHERE is_online = TRUE AND last_activity >= ?");
            $stmt_admin->execute([$activity_threshold_time]);
            $is_admin_available = $stmt_admin->fetchColumn() > 0;

            if (!$is_admin_available) {
                $gemini_api_key = $env["GEMINI_API_KEY"];

                $stmt_history = $pdo->prepare("SELECT sender_type, message_text FROM messages WHERE chat_session_id = ? ORDER BY timestamp DESC LIMIT 5");
                $stmt_history->execute([$session_id]);
                $history = $stmt_history->fetchAll(PDO::FETCH_ASSOC);
                $chat_history_for_prompt = "";
                foreach (array_reverse($history) as $msg) {
                    // Pastikan riwayat percakapan tidak ter-escape HTML jika memang ada HTML yang disengaja sebelumnya
                    $role = ($msg['sender_type'] === 'customer') ? "user" : "model";
                    $chat_history_for_prompt .= "{$role}: {$msg['message_text']}\n";
                }

                $db_context = "";

                // ==== Cek Keyword kendaraan ====
                $keywords = ['tersedia', 'ada', 'harga', 'termurah', 'termahal', 'dijual', 'rekomendasi', 'kendaraan', 'akhir tahun'];
                $found_keyword = false;
                foreach ($keywords as $keyword) {
                    if (stripos($message_text, $keyword) !== false) {
                        $found_keyword = true;
                        break;
                    }
                }

                if ($found_keyword) {
                    $query = "SELECT v.id, vm.name AS model_name, b.name AS brand_name, v.price_displayed, v.status, v.production_year FROM vehicles v JOIN vehicle_models vm ON v.vehicle_model_id = vm.id JOIN brands b ON vm.brand_id = b.id WHERE v.status = 'available' ORDER BY v.price_displayed ASC LIMIT 5";
                    if (stripos($message_text, 'termahal') !== false) {
                        $query = str_replace('ASC', 'DESC', $query);
                    }
                    $stmt_vehicles = $pdo->query($query);
                    $vehicles_data = $stmt_vehicles->fetchAll(PDO::FETCH_ASSOC);
                    if ($vehicles_data) {
                        $db_context .= "Berikut adalah data kendaraan dari database: " . json_encode($vehicles_data);
                    }
                }

                // ==== Cek Keyword pembayaran ====
                $payment_keywords = ['pembayaran', 'bayar', 'transfer', 'metode', 'cicilan', 'tunai'];
                $found_payment = false;
                foreach ($payment_keywords as $keyword) {
                    if (stripos($message_text, $keyword) !== false) {
                        $found_payment = true;
                        break;
                    }
                }

                if ($found_payment) {
                    $stmt_banks = $pdo->query("SELECT bank_name, account_number, account_name FROM banks WHERE is_active = 1");
                    $banks_data = $stmt_banks->fetchAll(PDO::FETCH_ASSOC);
                    if ($banks_data) {
                        $db_context .= "\nBerikut data bank untuk metode transfer: " . json_encode($banks_data) . ".";
                    }
                }

                $prompt = "Kamu adalah 'SiVeloz', asisten AI customer service untuk dealer kendaraan 'Akbar Veloz Motor'. Jawab dengan ramah, informatif, dan dalam Bahasa Indonesia.

---

Aturan dan Pengetahuanmu:

1.  Jawaban tidak boleh mengandung simbol bintang (*). Selalu gunakan tag HTML `<ul><li>...</li></ul>` untuk daftar atau paragraf rapi. Jangan gunakan simbol bullet point non-HTML.
2.  Nama kamu adalah SiVeloz.
3.  Jika disapa 'pagi', 'siang', 'sore', balas dengan ramah sesuai waktu.
4.  Pemesanan Test Drive/Transaksi bisa dilakukan melalui:
    <ul>
        <li>Form di halaman 'Hubungi Kami' di website</li>
        <li>Atau langsung lewat chat ini dengan melakukan negosiasi terlebih dahulu.</li>
    </ul>
5.  Jika pengguna menyebut kata 'negosiasi', 'menawar', atau 'budget':
    - Balas dengan dua bagian:
        1. Tuliskan: Untuk melakukan negosiasi klik opsi Motor dengan Budget Murah!
        2. Baris baru: [TRIGGER_NEGOTIATION]
6.  Jawab pertanyaan kendaraan menggunakan data dari database jika tersedia.
7.  Jika pengguna menanyakan terkait Rekomendasi Kendaraan Akhir Tahun, rekomendasikan kendaraan yang cocok. Sajikan informasi dalam format daftar menggunakan tag HTML `<ul>` dan `<li>`. Contoh:
    <ul>
        <li>[Nama Kendaraan 1]: [Deskripsi/Harga]</li>
        <li>[Nama Kendaraan 2]: [Deskripsi/Harga]</li>
    </ul>
8.  Jika ditanya cara pembayaran, jelaskan:
    Tersedia 2 cara: lunas dan cicilan.
    Metode pembayaran: tunai, Midtrans, dan transfer bank.
    Jika transfer bank, tampilkan daftar bank aktif dari database dalam format daftar menggunakan tag HTML `<ul>` dan `<li>`:
    <ul>
        <li>Nama Bank: [Nama Bank]<br>Nomor Rekening: [Nomor Rekening]<br>Atas Nama: [Atas Nama]</li>
        <li>Nama Bank: [Nama Bank Lain]<br>Nomor Rekening: [Nomor Rekening Lain]<br>Atas Nama: [Atas Nama Lain]</li>
    </ul>
9.  Jika ditanya lokasi showroom, jawab:
    Kami memiliki 2 cabang:
    <ul>
        <li>Cabang utama berada di lokasi Jl. Raya Desa Munjul, No 1, RT 08 RW 04, DS. Munjul, Kec. Pagaden Barat, Kab. Subang 41252 <a href='https://www.google.com/maps/@-6.4628592,107.7643725,3a,90y,13.15h,84.51t/data=!3m7!1e1!3m5!1sHrNH-CcwovEzFNZ4uIS6Cg!2e0!6shttps:%2F%2Fstreetviewpixels-pa.googleapis.com%2Fv1%2Fthumbnail%3Fcb_client%3Dmaps_sv.tactile%26w%3D900%26h%3D600%26pitch%3D5.489226069246428%26panoid%3DHrNH-CcwovEzFNZ4uIS6Cg%26yaw%3D13.148024439918533!7i16384!8i8192?entry=ttu&g_ep=EgoyMDI1MDYzMC4wIKXMDSoASAFQAw%3D%3D' target='_blank'>Lihat di Google Maps</a></li>
        <li>Cabang kedua berada di lokasi Jl. Raya Desa Munjul, No 1, RT 08 RW 04, DS. Munjul, Kec. Pagaden Barat, Kab. Subang 41252 <a href='https://www.google.com/maps/@-6.4551631,107.8118004,3a,75y,45.3h,78.35t/data=!3m7!1e1!3m5!1s8CGjmLx-pEK_phI4QiPApA!2e0!6shttps:%2F%2Fstreetviewpixels-pa.googleapis.com%2Fv1%2Fthumbnail%3Fcb_client%3Dmaps_sv.tactile%26w%3D900%26h%3D600%26pitch%3D11.650000000000006%26panoid%3D8CGjmLx-pEK_phI4QiPApA%26yaw%3D45.3!7i16384!8i8192?entry=ttu&g_ep=EgoyMDI1MDYzMC4wIKXMDSoASAFQAw%3D%3D' target='_blank'>Lihat di Google Maps</a></li>
    </ul>
10. Jika kamu tidak tahu jawabannya, cukup katakan: 'Maaf, untuk pertanyaan tersebut, silakan tunggu balasan dari admin kami ya.'

---

Konteks Data dari Database (jika ada):

{$db_context}

---

Riwayat Percakapan Terakhir:

{$chat_history_for_prompt}

---

Pertanyaan Pengguna Sekarang:

user: {$message_text}

---

Jawabanmu (sebagai SiVeloz):

model: ";

                $gemini_response = callGeminiAPI($gemini_api_key, $prompt);

                if ($gemini_response) {
                    $responses = explode('[TRIGGER_NEGOTIATION]', $gemini_response);
                    $message_clean = trim($responses[0]);

                    // Perbaikan: Hanya htmlspecialchars jika bukan tag HTML yang disengaja
                    // Ini adalah contoh sederhana, untuk produksi disarankan menggunakan pustaka sanitasi HTML yang lebih robust.
                    // Menambahkan pengecekan untuk <ul> dan <li>
                    $final_message_text = $message_clean;
                    if (!preg_match('/<a\s+href=.*?>.*?<\/a>/i', $message_clean) && !preg_match('/<ul.*?>.*?<\/ul>/is', $message_clean) && !preg_match('/<ol.*?>.*?<\/ol>/is', $message_clean)) {
                        $final_message_text = htmlspecialchars($message_clean);
                    }


                    if (!empty($final_message_text)) {
                        $stmt_ai_text = $pdo->prepare("INSERT INTO messages (chat_session_id, sender_id, sender_type, message_text, is_read_by_user, is_read_by_customer) VALUES (?, ?, 'bot', ?, 0, 1)");
                        $stmt_ai_text->execute([$session_id, $sender_id, $final_message_text]);
                        $ai_responses[] = [
                            'message_text' => $final_message_text,
                            'sender_type' => 'bot'
                        ];
                    }

                    if (count($responses) > 1 || str_contains($gemini_response, '[TRIGGER_NEGOTIATION]')) {
                        $trigger_response = '<strong>Cari motor sesuai budget?</strong><div class="promo-card" data-target="budget"><h4>Motor dengan Budget Murah!</h4><p>Temukan motor sesuai budget Anda</p></div>';

                        $stmt_trigger = $pdo->prepare("INSERT INTO messages (chat_session_id, sender_id, sender_type, message_text, is_read_by_user, is_read_by_customer) VALUES (?, ?, 'bot', ?, 0, 1)");
                        $stmt_trigger->execute([$session_id, $sender_id, $trigger_response]);

                        $ai_responses[] = [
                            'message_text' => $trigger_response,
                            'sender_type' => 'bot'
                        ];
                    }
                }
            }
        }

        $response_data = ['success' => true, 'message' => 'Pesan berhasil dikirim.'];
        if (!empty($ai_responses)) {
            $response_data['ai_messages'] = $ai_responses;
        }

        echo json_encode($response_data);
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
            }
        }
        if (isset($_SESSION['customer_id'])) {
            try {
                $stmt_update_activity = $pdo->prepare("UPDATE chat_sessions SET last_customer_activity = NOW() WHERE session_id = ?");
                $stmt_update_activity->execute([$session_id]);
            } catch (\PDOException $e) {
            }
        }

        try {
            $stmt = $pdo->prepare("SELECT id, sender_type, message_text, timestamp FROM messages WHERE chat_session_id = ? ORDER BY timestamp ASC");
            $stmt->execute([$session_id]);
            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'messages' => $messages]);
        } catch (\PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Gagal mengambil riwayat chat: ' . $e->getMessage()]);
        }
        break;

    case 'get_new_messages_customer':
        if (!checkCustomerAuth($pdo)) {
            exit();
        }
        $session_id = $_GET['session_id'] ?? '';
        $is_chat_active_on_frontend = filter_var($_GET['is_chat_active'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if (empty($session_id)) {
            echo json_encode(['success' => false, 'message' => 'ID sesi tidak boleh kosong.']);
            exit();
        }

        try {
            $pdo->beginTransaction();

            $stmt_update_activity = $pdo->prepare("UPDATE chat_sessions SET last_customer_activity = NOW() WHERE session_id = ?");
            $stmt_update_activity->execute([$session_id]);

            $stmt = $pdo->prepare("SELECT id, sender_type, message_text, timestamp FROM messages WHERE chat_session_id = ? AND (sender_type = 'user' OR sender_type = 'bot') AND is_read_by_customer = FALSE ORDER BY timestamp ASC");
            $stmt->execute([$session_id]);
            $new_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($new_messages) && $is_chat_active_on_frontend) {
                $message_ids = array_column($new_messages, 'id');
                $placeholders = implode(',', array_fill(0, count($message_ids), '?'));
                $stmt_mark_read = $pdo->prepare("UPDATE messages SET is_read_by_customer = TRUE WHERE id IN ($placeholders)");
                $stmt_mark_read->execute($message_ids);
            }

            $pdo->commit();

            echo json_encode(['success' => true, 'new_messages' => $new_messages]);
        } catch (\PDOException $e) {
            $pdo->rollBack();
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

    case 'mark_as_read_customer':
        if (!checkCustomerAuth($pdo)) {
            exit();
        }
        $session_id = $input['session_id'] ?? '';
        if (empty($session_id)) {
            echo json_encode(['success' => false, 'message' => 'ID sesi tidak boleh kosong.']);
            exit();
        }
        try {
            $stmt = $pdo->prepare("UPDATE messages SET is_read_by_customer = TRUE WHERE chat_session_id = ? AND (sender_type = 'user' OR sender_type = 'bot') AND is_read_by_customer = FALSE");
            $stmt->execute([$session_id]);
            echo json_encode(['success' => true, 'message' => 'Pesan ditandai sudah dibaca oleh customer.']);
        } catch (\PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Gagal menandai pesan sudah dibaca: ' . $e->getMessage()]);
        }
        break;

    case 'get_unread_count':
        if (!checkCustomerAuth($pdo)) {
            exit();
        }
        $customer_id = $_SESSION['customer_id'];
        $session_id = $_GET['session_id'] ?? '';

        try {
            $unread_count = 0;
            if (!empty($session_id)) {
                $stmt = $pdo->prepare("SELECT COUNT(id) FROM messages WHERE chat_session_id = ? AND (sender_type = 'user' OR sender_type = 'bot') AND is_read_by_customer = FALSE");
                $stmt->execute([$session_id]);
                $unread_count = $stmt->fetchColumn();
            } else {
                $recent_session_threshold = date('Y-m-d H:i:s', strtotime('-24 hour'));

                $stmt = $pdo->prepare("
                    SELECT COUNT(m.id)
                    FROM messages m
                    JOIN chat_sessions cs ON m.chat_session_id = cs.session_id
                    WHERE cs.customer_id = ?
                    AND (m.sender_type = 'user' OR m.sender_type = 'bot')
                    AND m.is_read_by_customer = FALSE
                    AND cs.started_at >= ?
                ");
                $stmt->execute([$customer_id, $recent_session_threshold]);
                $unread_count = $stmt->fetchColumn();
            }

            echo json_encode(['success' => true, 'unread_count' => $unread_count]);
        } catch (\PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Gagal mengambil jumlah pesan belum dibaca: ' . $e->getMessage()]);
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

            $base_image_url = $DOMAIN . '/storage/';

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

        // case 'create_test_drive_order':
        //     if (!checkCustomerAuth($pdo)) {
        //         exit();
        //     }

        //     $customer_id = $_SESSION['customer_id'];
        //     $vehicle_id = $input['vehicle_id'] ?? null;
        //     $negotiated_price = $input['negotiated_price'] ?? 0.00;

        //     if (empty($vehicle_id)) {
        //         echo json_encode(['success' => false, 'message' => 'ID kendaraan tidak boleh kosong.']);
        //         exit();
        //     }

        //     $checkVehicleIdInOrders = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE vehicle_id = ? AND status = 'proced'");
        //     $checkVehicleIdInOrders->execute([$vehicle_id]);
        //     $alreadyExists = $checkVehicleIdInOrders->fetchColumn();

        //     if ($alreadyExists) {
        //         echo json_encode(['success' => false, 'message' => 'Pesanan sudah dibuat sebelumnya untuk kendaraan ini.']);
        //         exit;
        //     }

        //     try {
        //         $pdo->beginTransaction();

        //         $stmt = $pdo->prepare("INSERT INTO orders (customer_id, vehicle_id, negotiated_price, type_order, status, is_read, created_at) VALUES (?, ?, ?, 'test_driver', 'proced', 0, NOW())");
        //         $stmt->execute([$customer_id, $vehicle_id, $negotiated_price]);
        //         $order_id = $pdo->lastInsertId();

        //         $InsertTestDriver = $pdo->prepare("INSERT INTO test_drivers (order_id, status, created_at) VALUES (?, 'process', NOW())");
        //         $InsertTestDriver->execute([$order_id]);

        //         $stmt_update_vehicle_status = $pdo->prepare("UPDATE vehicles SET status = 'test_drive' WHERE id = ?");
        //         $stmt_update_vehicle_status->execute([$vehicle_id]);

        //         $pdo->commit();
        //         echo json_encode(['success' => true, 'message' => 'Order Lanjut Test Drive berhasil dibuat.', 'order_id' => $order_id]);
        //     } catch (\PDOException $e) {
        //         $pdo->rollBack();
        //         echo json_encode(['success' => false, 'message' => 'Gagal membuat order Lanjut Test Drive: ' . $e->getMessage()]);
        //     }
        //     break;

        // case 'create_transaction_order':
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

        $checkVehicleIdInOrders = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE vehicle_id = ? AND status = 'proced'");
        $checkVehicleIdInOrders->execute([$vehicle_id]);
        $alreadyExists = $checkVehicleIdInOrders->fetchColumn();

        if ($alreadyExists) {
            echo json_encode(['success' => false, 'message' => 'Pesanan sudah dibuat sebelumnya untuk kendaraan ini.']);
            exit;
        }

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO orders (customer_id, vehicle_id, negotiated_price, type_order, status, is_read, created_at) VALUES (?, ?, ?, 'transaction', 'proced', 0, NOW())");
            $stmt->execute([$customer_id, $vehicle_id, $negotiated_price]);
            $order_id = $pdo->lastInsertId();

            $getPriceVehicle = $pdo->prepare("SELECT price_displayed FROM vehicles WHERE id = ?");
            $getPriceVehicle->execute([$vehicle_id]);
            $vehicle_price = $getPriceVehicle->fetchColumn();

            $insertTransaction = $pdo->prepare("INSERT INTO transactions (order_id, vehicle_price, deal_negotiation, grand_total, payment_type, payment_method, status, created_at) VALUES (?, ?, 0, 0, 'tunai', 'cash', 'pending', NOW())");
            $insertTransaction->execute([$order_id, $vehicle_price]);

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
