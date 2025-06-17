<?php
session_start();
include_once 'db_connect.php';
include '../helpers/functionGenerateSlug.php';

use App\GoogleOAuth;

$action = $_GET['action'] ?? '';
$input = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? $action;
    header('Content-Type: application/json');
}

error_log("Auth API: Request received. Action: " . $action . ", Method: " . $_SERVER['REQUEST_METHOD']);

switch ($action) {
    case 'register':
        $email = $input['email'] ?? '';
        $password = $input['password'] ?? '';
        $username = $input['username'] ?? null;
        $name = $input['name'] ?? $username;

        if (empty($name)) {
            $name = $email;
        }

        if (empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Email dan password tidak boleh kosong.']);
            exit();
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("INSERT INTO customers (username, email, password, google_id, facebook_id, name, is_logged_in, registration_method) VALUES (?, ?, ?, ?, ?, ?, FALSE, 'manual')");
            if ($stmt->execute([$username, $email, $hashed_password, null, null, $name])) {
                echo json_encode(['success' => true, 'message' => 'Pendaftaran berhasil! Silakan login.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal mendaftar. Email mungkin sudah terdaftar.']);
            }
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                echo json_encode(['success' => false, 'message' => 'Email ini sudah terdaftar.']);
            } else {
                error_log("Register database error: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Kesalahan database: ' . $e->getMessage()]);
            }
        }
        break;

    case 'login':
        $email = $input['email'] ?? '';
        $password = $input['password'] ?? '';

        if (empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Email dan password tidak boleh kosong.']);
            exit();
        }

        try {
            $stmt = $pdo->prepare("SELECT id, username, email, password, google_id, facebook_id, name FROM customers WHERE email = ?");
            $stmt->execute([$email]);
            $customer = $stmt->fetch();

            if ($customer && password_verify($password, $customer['password'])) {
                $_SESSION['customer_id'] = $customer['id'];
                $_SESSION['customer_email'] = $customer['email'];
                $_SESSION['customer_username'] = $customer['username'] ?? $customer['name'];

                $stmt_update_customer_login = $pdo->prepare("UPDATE customers SET is_logged_in = TRUE WHERE id = ?");
                $stmt_update_customer_login->execute([$customer['id']]);
                error_log("Regular Login: Customer ID " . $customer['id'] . " is_logged_in set to TRUE.");

                $redirectUrl = 'index.php';
                if (isset($_SESSION['redirect_to'])) {
                    $redirectUrl = $_SESSION['redirect_to'];
                    unset($_SESSION['redirect_to']);
                }
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Login berhasil!',
                    'redirect' => $redirectUrl 
                ]);

            } else {
                echo json_encode(['success' => false, 'message' => 'Email atau password salah.']);
            }
        } catch (\PDOException $e) {
            error_log("Login database error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Kesalahan database: ' . $e->getMessage()]);
        }
        break;

    case 'google_login':
        error_log("Google Login: Redirecting to Google auth URL.");
        $googleOAuth = new GoogleOAuth();
        $authUrl = $googleOAuth->getAuthUrl();
        header('Location: ' . $authUrl);
        exit();
        break;

    case 'google_callback':
        $redirectUrl = '../index.php';
        if (isset($_SESSION['redirect_to'])) {
            $redirectUrl = $_SESSION['redirect_to'];
            unset($_SESSION['redirect_to']);
        }
    
        error_log("Google Callback: Initiated. Method: " . $_SERVER['REQUEST_METHOD'] . ", Code received: " . ($_GET['code'] ?? 'N/A'));

        $googleOAuth = new GoogleOAuth();
        if (isset($_GET['code'])) {
            try {
                $token = $googleOAuth->fetchAccessTokenWithAuthCode($_GET['code']);
                if (isset($token['error'])) {
                    error_log("Google login error token: " . ($token['error_description'] ?? 'No description') . " (Error: " . ($token['error'] ?? 'N/A') . ")");
                    header('Location: ../login.php?error=' . urlencode('Gagal login dengan Google: ' . ($token['error_description'] ?? 'Kesalahan tidak diketahui.')));
                    exit();
                }
                error_log("Google Callback: Access token successfully fetched.");

                $googleOAuth->setAccessToken($token);
                $googleUser = $googleOAuth->getUserProfile();
                error_log("Google Callback: User profile successfully fetched. Email: " . $googleUser->getEmail());

                $googleId = $googleUser->getId();
                $email = $googleUser->getEmail();
                $name = $googleUser->getName();
                $slug = generateSlug($name);

                $stmt = $pdo->prepare("SELECT id, username, email, name FROM customers WHERE google_id = ?");
                $stmt->execute([$googleId]);
                $customer = $stmt->fetch();

                if ($customer) {
                    error_log("Google Callback: Existing customer found by google_id: " . $customer['id']);
                    $_SESSION['customer_id'] = $customer['id'];
                    $_SESSION['customer_email'] = $customer['email'];
                    $_SESSION['customer_username'] = $customer['username'] ?? $customer['name'];

                    $stmt_update_customer_login = $pdo->prepare("UPDATE customers SET is_logged_in = TRUE WHERE id = ?");
                    if ($stmt_update_customer_login->execute([$customer['id']])) {
                        error_log("Google Callback: is_logged_in set to TRUE for existing Google customer: " . $customer['id']);
                    } else {
                        error_log("Google Callback: FAILED to update is_logged_in for existing Google customer " . $customer['id'] . ". Error Info: " . json_encode($stmt_update_customer_login->errorInfo()));
                    }
                    
                    header('Location: ' . $redirectUrl);
                    exit();

                } else {
                    error_log("Google Callback: Customer not found by google_id. Checking by email: " . $email);
                    $stmt = $pdo->prepare("SELECT id, username, email, name FROM customers WHERE email = ?");
                    $stmt->execute([$email]);
                    $existingCustomerByEmail = $stmt->fetch();

                    if ($existingCustomerByEmail) {
                        error_log("Google Callback: Email found. Linking Google account to existing customer ID: " . $existingCustomerByEmail['id']);
                        $stmt_update = $pdo->prepare("UPDATE customers SET google_id = ?, is_logged_in = TRUE, registration_method = 'google' WHERE id = ?");
                        if ($stmt_update->execute([$googleId, $existingCustomerByEmail['id']])) {
                            $_SESSION['customer_id'] = $existingCustomerByEmail['id'];
                            $_SESSION['customer_email'] = $existingCustomerByEmail['email'];
                            $_SESSION['customer_username'] = $existingCustomerByEmail['username'] ?? $existingCustomerByEmail['name'];
                            error_log("Google Callback: is_logged_in set to TRUE for linked customer: " . $existingCustomerByEmail['id']);
                            
                            header('Location: ' . $redirectUrl);
                            exit();
                        } else {
                            error_log("Google Callback: FAILED to update is_logged_in for linked customer " . $existingCustomerByEmail['id'] . ". Error Info: " . json_encode($stmt_update->errorInfo()));
                            header('Location: ../login.php?error=' . urlencode('Gagal menautkan akun Google ke akun yang sudah ada.'));
                            exit();
                        }
                    } else {
                        error_log("Google Callback: Email not found. Creating new customer for: " . $email);
                        $stmt_insert = $pdo->prepare("INSERT INTO customers (username, email, password, google_id, is_logged_in, name, slug, registration_method, created_at, updated_at) VALUES (?, ?, NULL, ?, TRUE, ?, ?, 'google', NOW(), NOW())");
                        if ($stmt_insert->execute([$name, $email, $googleId, $name, $slug])) {
                            $newCustomerId = $pdo->lastInsertId();
                            $_SESSION['customer_id'] = $newCustomerId;
                            $_SESSION['customer_email'] = $email;
                            $_SESSION['customer_username'] = $name;
                            error_log("Google Callback: New customer created with ID: " . $newCustomerId . " and is_logged_in set to TRUE.");

                            header('Location: ' . $redirectUrl);
                            exit();
                        } else {
                            error_log("Google Callback: FAILED to create new customer with is_logged_in set. Error Info: " . json_encode($stmt_insert->errorInfo()));
                            header('Location: ../login.php?error=' . urlencode('Gagal membuat akun dengan Google.'));
                            exit();
                        }
                    }
                }
            } catch (\Exception $e) {
                error_log("Google callback general error: " . $e->getMessage() . " on line " . $e->getLine() . " in " . $e->getFile() . ". Stack trace: " . $e->getTraceAsString());
                header('Location: ../login.php?error=' . urlencode('Terjadi kesalahan saat memproses login Google. Silakan coba lagi.'));
                exit();
            }
        } else {
            error_log("Google callback: No 'code' received in GET request. Check if customer denied access or state mismatch.");
            header('Location: ../login.php?error=' . urlencode('Akses Google ditolak atau kode otorisasi tidak diterima.'));
            exit();
        }
        break;

    case 'logout':
        if (isset($_SESSION['customer_id'])) {
            try {
                $stmt_update_customer_logout = $pdo->prepare("UPDATE customers SET is_logged_in = FALSE WHERE id = ?");
                $stmt_update_customer_logout->execute([$_SESSION['customer_id']]);
                error_log("Customer Logout: Customer ID " . $_SESSION['customer_id'] . " is_logged_in set to FALSE.");

                $stmt_update_session_on_logout = $pdo->prepare("
                    UPDATE chat_sessions
                    SET status = 'closed', last_customer_activity = NULL, customer_typing = FALSE
                    WHERE customer_id = ?
                    ORDER BY started_at DESC LIMIT 1
                ");
                $stmt_update_session_on_logout->execute([$_SESSION['customer_id']]);

            } catch (\PDOException $e) {
                error_log("Error updating customer status on logout: " . $e->getMessage());
            }
        }
        session_unset();
        session_destroy();
        echo json_encode(['success' => true, 'message' => 'Logout berhasil.']);
        break;

    default:
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            echo "Aksi tidak valid atau tidak ditemukan.";
            exit();
        }
        echo json_encode(['success' => false, 'message' => 'Aksi tidak valid.']);
        break;
}