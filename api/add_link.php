<?php
require_once 'db.php';

$userId = requireAuth();

// Check for POST data
$data = json_decode(file_get_contents("php://input"));

if (isset($data->id) && isset($data->name) && isset($data->url)) {
    try {
        $stmt = $pdo->prepare("INSERT INTO links (id, user_id, name, url, icon) VALUES (:id, :user_id, :name, :url, :icon)");
        $icon = isset($data->icon) ? $data->icon : '';
        
        $stmt->execute([
            ':id' => $data->id,
            ':user_id' => $userId,
            ':name' => $data->name,
            ':url' => $data->url,
            ':icon' => $icon
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Lối tắt đã được thêm thành công']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
}
?>
