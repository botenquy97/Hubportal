<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Thư mục lưu ảnh, tính từ gốc project
$uploadDir = dirname(__DIR__) . '/uploads/';

// Tạo thư mục nếu chưa có
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        echo json_encode(['success' => false, 'message' => 'Không thể tạo thư mục uploads/. Hãy kiểm tra quyền ghi của thư mục gốc.']);
        exit;
    }
}

if (!isset($_FILES['icon'])) {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy dữ liệu file ($_FILES empty).']);
    exit;
}

if ($_FILES['icon']['error'] !== UPLOAD_ERR_OK) {
    $errorMsg = 'Lỗi upload số ' . $_FILES['icon']['error'];
    if ($_FILES['icon']['error'] === 1 || $_FILES['icon']['error'] === 2) $errorMsg = 'File quá lớn (vượt giới hạn PHP server).';
    echo json_encode(['success' => false, 'message' => $errorMsg]);
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
    // Trả về đường dẫn tương đối (không có .\/ để tránh nhầm lẫn)
    echo json_encode([
        'success' => true,
        'icon_path' => 'uploads/' . $newFileName
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi khi lưu file. Kiểm tra quyền thư mục uploads/.']);
}
?>
