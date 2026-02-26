<?php
// Config: Điền thông tin Database cPanel vào đây
$host = 'localhost'; 
$dbname = 'gtxjozdehosting_hubportal'; // Thay tên DB của bạn
$username = 'gtxjozdehosting_hubportal'; // Thay Username của bạn
$password = 'Spencil@123'; // Thay Mật khẩu của bạn

// Cấu hình Session duy trì trong 24 giờ
$sessionLifetime = 24 * 60 * 60;
session_set_cookie_params($sessionLifetime);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'message' => "Kết nối CSDL thất bại: " . $e->getMessage()]));
}

// Hàm kiểm tra người dùng đã đăng nhập chưa
function getCurrentUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

function requireAuth() {
    $userId = getCurrentUserId();
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Yêu cầu đăng nhập', 'require_login' => true]);
        exit;
    }
    return $userId;
}
?>
