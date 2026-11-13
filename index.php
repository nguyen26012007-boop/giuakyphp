<?php
require __DIR__ . '/config.php';
require __DIR__ . '/view.php';
$users = $pdo->query('SELECT admin_id, admin_email, images FROM Users ORDER BY admin_id DESC')->fetchAll();
page_start('directory');
?>
<section class="page-heading">
    <div><p class="eyebrow">DANH BẠ NHÂN SỰ</p><h1>Danh sách người dùng<span class="heading-dot">.</span></h1><p class="description">Thông tin của mọi người, được sắp xếp tại một nơi.</p></div>
    <a class="button primary" href="admin.php#user-form"><?= ui_icon('plus') ?>Thêm người dùng</a>
</section>
<section class="directory-intro" aria-label="Tổng quan danh sách">
    <div class="summary-icon"><?= ui_icon('users') ?></div>
    <div><p class="summary-label">Tổng số người dùng</p><p class="summary-value"><?= count($users) ?><span>người dùng</span></p></div>
    <div class="summary-description"><strong>Một danh bạ, dễ quản lý.</strong><p>Tra cứu email và xem thông tin người dùng trong hệ thống.</p></div>
</section>
<section aria-labelledby="directory-title" data-user-list>
    <div class="list-toolbar"><div><h2 id="directory-title">Tất cả người dùng <span class="count"><?= count($users) ?></span></h2><p id="result-count" class="subtle" role="status" aria-live="polite"><?= count($users) ?> người dùng trong danh sách</p></div>
        <div class="search-field"><?= ui_icon('search') ?><label for="user-search" class="sr-only">Tìm người dùng theo email hoặc mã ID</label><input id="user-search" type="search" placeholder="Tìm email hoặc mã ID…" data-user-search aria-controls="directory-items"></div>
    </div>
    <div class="user-grid" id="directory-items">
        <?php foreach ($users as $user): ?>
        <article class="user-card" data-filter-item data-search="<?= e($user['admin_email'] . ' ' . $user['admin_id']) ?>">
            <div class="user-card-top"><img class="portrait" src="<?= e(avatar($user['images'])) ?>" width="72" height="72" alt="Ảnh đại diện của <?= e($user['admin_email']) ?>" loading="lazy"><span class="id-badge">ID #<?= e($user['admin_id']) ?></span></div>
            <p class="card-label">ĐỊA CHỈ EMAIL</p><h3><?= e($user['admin_email']) ?></h3>
            <div class="user-card-footer"><span><?= ui_icon('mail') ?>Người dùng</span><a class="text-button" href="admin.php?edit=<?= e($user['admin_id']) ?>" aria-label="Chỉnh sửa <?= e($user['admin_email']) ?>">Chỉnh sửa<?= ui_icon('arrow') ?></a></div>
        </article>
        <?php endforeach; ?>
    </div>
    <?php if (!$users): ?>
    <div class="empty-state"><span class="empty-icon"><?= ui_icon('users') ?></span><h3>Danh bạ đang chờ người đầu tiên</h3><p>Thêm người dùng để bắt đầu quản lý thông tin.</p><a class="button primary" href="admin.php#user-form"><?= ui_icon('plus') ?>Thêm người dùng</a></div>
    <?php endif; ?>
    <div class="empty-state" data-no-results hidden><span class="empty-icon"><?= ui_icon('search') ?></span><h3>Không tìm thấy người dùng</h3><p>Thử tìm bằng email hoặc mã ID khác.</p><button type="button" class="button secondary" data-clear-search>Xóa tìm kiếm</button></div>
</section>
<?php page_end(); ?>
