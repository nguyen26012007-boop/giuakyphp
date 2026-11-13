<?php
// Các thành phần giao diện dùng chung; không thay đổi dữ liệu người dùng.
function ui_icon(string $name, string $class = ''): string
{
    $paths = [
        'grid' => '<rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/>',
        'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2m20 0v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/><circle cx="9" cy="7" r="4"/>',
        'manage' => '<path d="m16 3 5 5-11 11-6 1 1-6L16 3Zm-2 2 5 5M3 5h6M3 9h4M3 23h18"/>',
        'plus' => '<path d="M12 5v14M5 12h14"/>',
        'user-plus' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2m18-13v6m-3-3h6"/><circle cx="9" cy="7" r="4"/>',
        'search' => '<circle cx="10.8" cy="10.8" r="7.3"/><path d="m16 16 5 5"/>',
        'arrow' => '<path d="M5 12h14m-6-6 6 6-6 6"/>',
        'chevron' => '<path d="m9 5 7 7-7 7"/>',
        'edit' => '<path d="m16 3 5 5-11 11-6 1 1-6L16 3Zm-2 2 5 5"/>',
        'trash' => '<path d="M3 6h18M9 6V3h6v3M5 6l1 15h12l1-15M10 10v7m4-7v7"/>',
        'mail' => '<rect x="3" y="5" width="18" height="14" rx="3"/><path d="m3 6 9 7 9-7"/>',
        'lock' => '<rect x="5" y="10" width="14" height="11" rx="3"/><path d="M8 10V7a4 4 0 0 1 8 0v3m-4 4v3"/>',
        'eye' => '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>',
        'upload' => '<path d="M12 16V3m-5 5 5-5 5 5M4 16v5h16v-5"/>',
        'check' => '<path d="m5 12 4 4L19 6"/>',
        'info' => '<circle cx="12" cy="12" r="9"/><path d="M12 11v6m0-10h.01"/>',
    ];
    return '<svg class="icon ' . e($class) . '" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . ($paths[$name] ?? $paths['grid']) . '</svg>';
}

function page_start(string $active): void
{
    $admin = $active === 'admin';
    $title = $admin ? 'Quản trị người dùng' : 'Danh sách người dùng';
    $version = (string)filemtime(__DIR__ . '/style.css');
    ?>
    <!doctype html>
    <html lang="vi">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#153e35">
        <title><?= e($title) ?> · QLNhanSu</title>
        <link rel="icon" href="logo.svg" type="image/svg+xml">
        <link rel="stylesheet" href="style.css?v=<?= e($version) ?>">
        <script src="app.js?v=<?= e((string)filemtime(__DIR__ . '/app.js')) ?>" defer></script>
    </head>
    <body>
    <a class="skip-link" href="#main-content">Đến nội dung chính</a>
    <aside class="sidebar">
        <a class="brand" href="index.php" aria-label="QLNhanSu — Trang danh sách">
            <span class="brand-mark"><?= ui_icon('grid') ?></span>
            <span>QLNhanSu<small>Quản lý người dùng</small></span>
        </a>
        <p class="nav-label">KHÔNG GIAN LÀM VIỆC</p>
        <nav class="navigation" aria-label="Điều hướng chính">
            <a href="index.php" class="nav-link <?= !$admin ? 'is-active' : '' ?>" <?= !$admin ? 'aria-current="page"' : '' ?>><?= ui_icon('users') ?><span>Danh sách người dùng</span></a>
            <a href="admin.php" class="nav-link <?= $admin ? 'is-active' : '' ?>" <?= $admin ? 'aria-current="page"' : '' ?>><?= ui_icon('manage') ?><span>Quản trị người dùng</span></a>
        </nav>
        <div class="sidebar-note"><span class="sidebar-note-icon"><?= ui_icon('users') ?></span><strong>Mọi người, một nơi.</strong><p>Quản lý thông tin người dùng<br>đơn giản và thuận tiện.</p></div>
        <div class="sidebar-footer"><span class="brand-dot"></span>Hệ thống QLNhanSu</div>
    </aside>
    <div class="workspace">
        <header class="topbar"><div class="breadcrumb"><span>Không gian làm việc</span><?= ui_icon('chevron') ?><strong>Người dùng</strong></div><span class="page-tag"><?= ui_icon($admin ? 'manage' : 'users') ?><?= $admin ? 'Quản trị' : 'Danh bạ' ?></span></header>
        <main id="main-content" tabindex="-1">
    <?php
}

function page_end(): void
{
    ?>
        </main>
        <footer class="page-footer"><span>QLNhanSu</span><span>Quản lý người dùng</span></footer>
    </div>
    </body>
    </html>
    <?php
}
