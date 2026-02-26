<?php
require_once 'db.php';

if (isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => true,
        'user' => [
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'picture' => $_SESSION['user_picture']
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
}
?>
