<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'api/db.php';

try {
    // 1. Tạo bảng users
    $sql_users = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        google_id VARCHAR(255) UNIQUE NOT NULL,
        email VARCHAR(255) NOT NULL,
        name VARCHAR(255),
        picture TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($sql_users);

    // 2. Thêm cột user_id vào bảng links
    // Kiểm tra xem cột đã tồn tại chưa để tránh lỗi
    $check_column = $pdo->query("SHOW COLUMNS FROM links LIKE 'user_id'")->fetch();
    if (!$check_column) {
        $pdo->exec("ALTER TABLE links ADD COLUMN user_id INT AFTER id;");
        $pdo->exec("ALTER TABLE links ADD CONSTRAINT fk_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;");
    }

    echo json_encode(['success' => true, 'message' => 'Cập nhật cơ sở dữ liệu thành công!']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi SQL: ' . $e->getMessage()]);
}
?>
