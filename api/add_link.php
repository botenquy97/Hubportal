<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once 'db.php';

// Check for POST data
$data = json_decode(file_get_contents("php://input"));

if (isset($data->id) && isset($data->name) && isset($data->url)) {
    try {
        $stmt = $pdo->prepare("INSERT INTO links (id, name, url, icon) VALUES (:id, :name, :url, :icon)");
        $icon = isset($data->icon) ? $data->icon : '';
        
        $stmt->execute([
            ':id' => $data->id,
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
