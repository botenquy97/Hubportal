<?php
require_once 'db.php';

$userId = getCurrentUserId();
if (!$userId) {
    echo json_encode(['success' => true, 'data' => []]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, name, url, icon FROM links WHERE user_id = :user_id ORDER BY created_at ASC");
    $stmt->execute([':user_id' => $userId]);
    $links = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $links]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
