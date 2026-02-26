<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Thư mục lưu ảnh, tính từ gốc project
$uploadDir = dirname(__DIR__) . '/uploads/';

// Tạo thư mục nếu chưa có
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if (!isset($_FILES['icon']) || $_FILES['icon']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Không nhận được file hợp lệ.']);
    exit;
}

$file = $_FILES['icon'];
$maxSize = 2 * 1024 * 1024; // 2MB
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];

if ($file['size'] > $maxSize) {
    echo json_encode(['success' => false, 'message' => 'File quá lớn. Tối đa 2MB.']);
    exit;
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mimeType, $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Định dạng file không được hỗ trợ. Chỉ chấp nhận JPG, PNG, GIF, WEBP, SVG.']);
    exit;
}

// Tạo tên file độc nhất để tránh trùng
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$newFileName = uniqid('icon_', true) . '.' . $ext;
$destPath = $uploadDir . $newFileName;

if (move_uploaded_file($file['tmp_name'], $destPath)) {
    // Trả về đường dẫn tương đối để lưu vào DB
    echo json_encode([
        'success' => true,
        'icon_path' => './uploads/' . $newFileName
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi khi lưu file. Kiểm tra quyền thư mục uploads/.']);
}
?>
