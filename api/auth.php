<?php
header('Content-Type: application/json');
require_once 'db.php';

// Nhận token từ frontend
$data = json_decode(file_get_contents("php://input"));
$idToken = isset($data->id_token) ? $data->id_token : null;

if (!$idToken) {
    echo json_encode(['success' => false, 'message' => 'Thiếu ID Token']);
    exit;
}

// Xác thực token với Google API
$verify_url = "https://oauth2.googleapis.com/tokeninfo?id_token=" . $idToken;
$response = file_get_contents($verify_url);
$payload = json_decode($response, true);

if (isset($payload['aud']) && $payload['aud'] === '1035735916475-e0jab5r601ltdg31h19foo8v27cfpbcq.apps.googleusercontent.com') {
    // Token hợp lệ
    $googleId = $payload['sub'];
    $email = $payload['email'];
    $name = $payload['name'];
    $picture = $payload['picture'];

    try {
        // Kiểm tra user đã tồn tại chưa
        $stmt = $pdo->prepare("SELECT id FROM users WHERE google_id = :google_id");
        $stmt->execute([':google_id' => $googleId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            // Tạo user mới
            $stmt = $pdo->prepare("INSERT INTO users (google_id, email, name, picture) VALUES (:google_id, :email, :name, :picture)");
            $stmt->execute([
                ':google_id' => $googleId,
                ':email' => $email,
                ':name' => $name,
                ':picture' => $picture
            ]);
            $userId = $pdo->lastInsertId();
        } else {
            $userId = $user['id'];
            // Cập nhật thông tin profile nếu có thay đổi
            $stmt = $pdo->prepare("UPDATE users SET name = :name, picture = :picture WHERE id = :id");
            $stmt->execute([':name' => $name, ':picture' => $picture, ':id' => $userId]);
        }

        // Tạo session
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_picture'] = $picture;

        echo json_encode([
            'success' => true, 
            'message' => 'Đăng nhập thành công',
            'user' => [
                'name' => $name,
                'email' => $email,
                'picture' => $picture
            ]
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Lỗi CSDL: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Token không hợp lệ hoặc đã hết hạn']);
}
?>
