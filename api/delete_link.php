<?php
require_once 'db.php';

$userId = requireAuth();

// Check for POST data
$data = json_decode(file_get_contents("php://input"));

if (isset($data->id)) {
    try {
        $stmt = $pdo->prepare("DELETE FROM links WHERE id = :id AND user_id = :user_id");
        $stmt->execute([
            ':id' => $data->id,
            ':user_id' => $userId
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Đã xóa lối tắt']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
}
?>
