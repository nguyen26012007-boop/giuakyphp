<?php
declare(strict_types=1);
session_start();
// Sửa các giá trị này theo cấu hình MySQL của bạn.
$host = getenv('DB_HOST') ?: '127.0.0.1';
$database = getenv('DB_NAME') ?: 'QLNhanSu';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $exception) {
    http_response_code(500);
    exit('Không kết nối được MySQL. Hãy nhập database.sql và kiểm tra cấu hình trong config.php.');
}
function e($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function csrf(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function check_csrf(): void {
    if (!hash_equals(csrf(), (string)($_POST['csrf'] ?? ''))) {
        http_response_code(403);
        exit('Phiên làm việc không hợp lệ. Vui lòng tải lại trang.');
    }
}
function avatar(string $path): string {
    return preg_match('~^uploads/[a-f0-9]{32}\.(jpg|png|webp|gif)$~D', $path) ? $path : 'avatar.svg';
}
function remove_image(string $path): void {
    if (avatar($path) === $path && is_file(__DIR__ . '/' . $path)) unlink(__DIR__ . '/' . $path);
}
function upload_image(): ?string {
    if (!isset($_FILES['images']) || $_FILES['images']['error'] === UPLOAD_ERR_NO_FILE) return null;
    $file = $_FILES['images'];
    if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > 2 * 1024 * 1024) {
        throw new RuntimeException('Ảnh phải tải lên thành công và không vượt quá 2 MB.');
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    if (!isset($extensions[$mime]) || !getimagesize($file['tmp_name'])) throw new RuntimeException('Chọn ảnh JPG, PNG, WebP hoặc GIF hợp lệ.');
    $dir = __DIR__ . '/uploads';
    if (!is_dir($dir) && !mkdir($dir, 0755, true)) throw new RuntimeException('Không tạo được thư mục uploads.');
    $path = 'uploads/' . bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
    if (!move_uploaded_file($file['tmp_name'], __DIR__ . '/' . $path)) throw new RuntimeException('Không lưu được ảnh.');
    return $path;
}
