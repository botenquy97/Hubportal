<?php
// Config: Điền thông tin Database cPanel vào đây
$host = 'localhost'; 
$dbname = 'gtxjozdehosting_hubportal'; // Thay tên DB của bạn
$username = 'gtxjozdehosting_hubportal'; // Thay Username của bạn
$password = 'Spencil@123'; // Thay Mật khẩu của bạn

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode([
        'success' => false, 
        'message' => 'Lỗi kết nối CSDL: ' . $e->getMessage()
    ]));
}
?>
