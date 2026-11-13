<?php
require __DIR__ . '/config.php';
require __DIR__ . '/view.php';
$error = '';
$editing = null;
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $newImage = null;
    try {
        $id = filter_var($_POST['admin_id'] ?? 0, FILTER_VALIDATE_INT);
        $action = $_POST['action'] ?? '';
        $existing = null;
        if ($id) {
            $stmt = $pdo->prepare('SELECT * FROM Users WHERE admin_id = ?');
            $stmt->execute([$id]);
            $existing = $stmt->fetch();
            if (!$existing) throw new RuntimeException('Người dùng không tồn tại.');
        }
        if ($action === 'delete' && $existing) {
            $stmt = $pdo->prepare('DELETE FROM Users WHERE admin_id = ?');
            $stmt->execute([$id]);
            remove_image($existing['images']);
            $_SESSION['notice'] = 'Đã xóa người dùng.';
        } elseif ($action === 'save') {
            $email = trim($_POST['admin_email'] ?? '');
            if (strlen($email) > 255 || !filter_var($email, FILTER_VALIDATE_EMAIL)) throw new RuntimeException('Nhập địa chỉ email hợp lệ, tối đa 255 ký tự.');
            $plainPassword = $_POST['admin_password'] ?? '';
            if ((!$existing || $plainPassword !== '') && (strlen($plainPassword) < 6 || strlen($plainPassword) > 72)) throw new RuntimeException('Mật khẩu phải từ 6 đến 72 byte.');
            $hash = $plainPassword !== '' ? password_hash($plainPassword, PASSWORD_DEFAULT) : $existing['admin_password'];
            $newImage = upload_image();
            $imagePath = $newImage ?? ($existing['images'] ?? '');
            if ($existing) {
                $stmt = $pdo->prepare('UPDATE Users SET admin_email = ?, admin_password = ?, images = ? WHERE admin_id = ?');
                $stmt->execute([$email, $hash, $imagePath, $id]);
                if ($newImage) remove_image($existing['images']);
            } else {
                $stmt = $pdo->prepare('INSERT INTO Users (admin_email, admin_password, images) VALUES (?, ?, ?)');
                $stmt->execute([$email, $hash, $imagePath]);
            }
            $_SESSION['notice'] = $existing ? 'Đã cập nhật người dùng.' : 'Đã thêm người dùng.';
        } else throw new RuntimeException('Thao tác không hợp lệ.');
        header('Location: admin.php');
        exit;
    } catch (Throwable $exception) {
        if ($newImage) remove_image($newImage);
        $error = $exception instanceof PDOException ? 'Không thể lưu dữ liệu. Vui lòng kiểm tra cơ sở dữ liệu.' : $exception->getMessage();
        if (($_POST['action'] ?? '') === 'save') $editing = $existing ?? null;
    }
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM Users WHERE admin_id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editing = $stmt->fetch() ?: null;
    if (!$editing) $error = 'Người dùng không tồn tại.';
    $email = $editing['admin_email'] ?? '';
}
$users = $pdo->query('SELECT admin_id, admin_email, images FROM Users ORDER BY admin_id DESC')->fetchAll();
$notice = $_SESSION['notice'] ?? ''; unset($_SESSION['notice']);
page_start('admin');
?>
<section class="page-heading">
    <div><p class="eyebrow">QUẢN TRỊ NHÂN SỰ</p><h1>Quản lý người dùng<span class="heading-dot">.</span></h1><p class="description">Thêm mới, cập nhật và quản lý thông tin thật dễ dàng.</p></div>
    <a class="button secondary" href="index.php">Xem danh sách<?= ui_icon('arrow') ?></a>
</section>
<?php if ($notice): ?><div class="alert success" role="status"><?= ui_icon('check') ?><span><?= e($notice) ?></span></div><?php endif; ?>
<?php if ($error): ?><div id="form-error" class="alert error" role="alert" tabindex="-1"><?= ui_icon('info') ?><span><?= e($error) ?></span></div><?php endif; ?>
<div class="admin-layout">
    <section class="panel form-panel" id="user-form" aria-labelledby="form-title">
        <div class="panel-heading"><span class="section-icon"><?= ui_icon($editing ? 'edit' : 'user-plus') ?></span><div><h2 id="form-title"><?= $editing ? 'Chỉnh sửa người dùng' : 'Thêm người dùng' ?></h2><p class="subtle"><?= $editing ? 'Cập nhật thông tin bên dưới.' : 'Điền thông tin để thêm vào danh sách.' ?></p></div></div>
        <form method="post" enctype="multipart/form-data" data-user-form>
            <input type="hidden" name="csrf" value="<?= e(csrf()) ?>"><input type="hidden" name="action" value="save">
            <div class="field"><label for="admin_id">Mã người dùng<span class="optional">Tự động</span></label><input id="admin_id" name="admin_id" value="<?= e($editing['admin_id'] ?? '') ?>" readonly placeholder="Được tạo khi thêm người dùng"></div>
            <div class="field"><label for="admin_email">Địa chỉ email <span class="required" aria-hidden="true">*</span></label><div class="input-with-icon"><?= ui_icon('mail') ?><input id="admin_email" type="email" name="admin_email" maxlength="255" value="<?= e($email) ?>" placeholder="vidu@email.com" autocomplete="email" required></div></div>
            <div class="field"><label for="admin_password">Mật khẩu <?php if (!$editing): ?><span class="required" aria-hidden="true">*</span><?php endif; ?></label><div class="input-with-icon password-field"><?= ui_icon('lock') ?><input id="admin_password" type="password" name="admin_password" minlength="6" maxlength="72" autocomplete="new-password" placeholder="Nhập mật khẩu" aria-describedby="password-hint" <?= $editing ? '' : 'required' ?>><button type="button" class="password-toggle" data-password-toggle aria-controls="admin_password" aria-label="Hiện mật khẩu" aria-pressed="false"><?= ui_icon('eye') ?></button></div><p class="field-hint" id="password-hint"><?= $editing ? 'Để trống nếu bạn muốn giữ mật khẩu hiện tại.' : 'Tối thiểu 6 ký tự, tối đa 72 byte.' ?></p></div>
            <div class="field"><label for="images">Ảnh đại diện<span class="optional">Không bắt buộc</span></label><div class="upload-box"><img id="image-preview" class="upload-preview" src="<?= e(avatar($editing['images'] ?? '')) ?>" width="60" height="60" alt="Xem trước ảnh đại diện"><div class="upload-details"><div class="file-trigger"><span><?= ui_icon('upload') ?>Chọn ảnh</span><input id="images" type="file" name="images" accept="image/jpeg,image/png,image/webp,image/gif" aria-describedby="image-hint image-error"></div><p id="file-name" class="file-name" aria-live="polite"><?= $editing && $editing['images'] ? 'Đang sử dụng ảnh hiện tại' : 'Chưa chọn ảnh' ?></p></div></div><p class="field-hint" id="image-hint">JPG, PNG, WebP hoặc GIF. Tối đa 2 MB.</p><p id="image-error" class="field-error" role="alert" hidden></p></div>
            <div class="form-actions"><button type="submit" class="button primary"><?= ui_icon($editing ? 'check' : 'plus') ?><?= $editing ? 'Lưu thay đổi' : 'Thêm người dùng' ?></button><?php if ($editing): ?><a class="button secondary" href="admin.php">Hủy chỉnh sửa</a><?php endif; ?></div>
        </form>
    </section>
    <section class="panel list-panel" data-user-list aria-labelledby="list-title">
        <div class="list-panel-heading"><div><p class="eyebrow">DANH SÁCH HIỆN TẠI</p><h2 id="list-title">Người dùng <span class="count"><?= count($users) ?></span></h2></div><span class="section-icon neutral"><?= ui_icon('users') ?></span></div>
        <div class="table-toolbar"><div class="search-field"><?= ui_icon('search') ?><label for="user-search" class="sr-only">Tìm người dùng theo email hoặc mã ID</label><input id="user-search" type="search" placeholder="Tìm email hoặc mã ID…" data-user-search aria-controls="admin-users"></div><p id="result-count" class="subtle" role="status" aria-live="polite"><?= count($users) ?> người dùng</p></div>
        <div class="table-wrap"><table role="table"><caption class="sr-only">Danh sách người dùng và thao tác quản lý</caption><thead role="rowgroup"><tr role="row"><th scope="col" role="columnheader">Mã ID</th><th scope="col" role="columnheader">Ảnh</th><th scope="col" role="columnheader">Địa chỉ email</th><th scope="col" role="columnheader" class="action-heading">Thao tác</th></tr></thead><tbody id="admin-users" role="rowgroup">
        <?php foreach ($users as $user): ?>
        <tr role="row" data-filter-item data-search="<?= e($user['admin_email'] . ' ' . $user['admin_id']) ?>">
            <td role="cell" class="id-cell"><span class="table-id">#<?= e($user['admin_id']) ?></span></td>
            <td role="cell" class="avatar-cell"><img class="table-avatar" src="<?= e(avatar($user['images'])) ?>" width="40" height="40" alt="Ảnh đại diện của <?= e($user['admin_email']) ?>" loading="lazy"></td>
            <td role="cell" class="email-cell"><?= e($user['admin_email']) ?></td>
            <td role="cell" class="actions-cell"><div class="row-actions"><a class="row-button edit-button" href="admin.php?edit=<?= e($user['admin_id']) ?>#user-form" aria-label="Sửa <?= e($user['admin_email']) ?>"><?= ui_icon('edit') ?><span>Sửa</span></a><form method="post" onsubmit="return confirm('Bạn có chắc muốn xóa người dùng này?')"><input type="hidden" name="csrf" value="<?= e(csrf()) ?>"><input type="hidden" name="admin_id" value="<?= e($user['admin_id']) ?>"><input type="hidden" name="action" value="delete"><button type="submit" class="row-button delete-button" aria-label="Xóa <?= e($user['admin_email']) ?>"><?= ui_icon('trash') ?><span>Xóa</span></button></form></div></td>
        </tr>
        <?php endforeach; ?>
        </tbody></table></div>
        <?php if (!$users): ?><div class="empty-state compact"><span class="empty-icon"><?= ui_icon('users') ?></span><h3>Chưa có người dùng</h3><p>Điền biểu mẫu để thêm người dùng đầu tiên.</p></div><?php endif; ?>
        <div class="empty-state compact" data-no-results hidden><span class="empty-icon"><?= ui_icon('search') ?></span><h3>Không tìm thấy người dùng</h3><p>Thử tìm bằng email hoặc mã ID khác.</p><button type="button" class="button secondary" data-clear-search>Xóa tìm kiếm</button></div>
        <div class="table-note"><?= ui_icon('info') ?><span>Chọn <strong>Sửa</strong> để cập nhật thông tin người dùng.</span></div>
    </section>
</div>
<?php page_end(); ?>
