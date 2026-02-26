<?php
header('Content-Type: application/json');
require_once 'db.php';

session_unset();
session_destroy();

echo json_encode(['success' => true, 'message' => 'Đã đăng xuất']);
?>
