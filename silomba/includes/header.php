
<?php

require_once __DIR__ . '/auth.php';

$user = current_user();

$page_title = $page_title ?? APP_NAME;
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= e($page_title) ?> | <?= e(APP_NAME) ?></title>

    <link rel="stylesheet"
          href="<?= e(url('assets/css/style.css')) ?>">

    <script src="<?= e(url('assets/js/main.js')) ?>" defer></script>
</head>
<body>

<div class="app-layout">

<header class="topbar">
    <div class="topbar-brand">
        <span class="brand-icon">
            <img src="<?= e(url('picture/logo%20silomba.png')) ?>" alt="Logo SILOMBA">
        </span>
        <div class="topbar-brand-text">
            <strong>SILOMBA</strong>
            <span>Sistem Informasi Lomba</span>
        </div>
    </div>

    <div class="topbar-center">
        <form class="topbar-search"
              action="<?= e(url('index.php')) ?>"
              method="get">
            <span class="search-icon">🔍</span>
            <input type="search"
                   name="q"
                   placeholder="Cari lomba, peserta, atau informasi..."
                   aria-label="Pencarian">
        </form>
    </div>

    <div class="topbar-user">
        <div class="topbar-user-info">
            <div class="topbar-user-name"><?= e($user['nama'] ?? 'Pengguna') ?></div>
            <div class="topbar-user-role"><?= e(ucfirst($user['role'] ?? '')) ?></div>
        </div>
        <div class="avatar">
            <?= e(strtoupper(substr($user['nama'] ?? 'U', 0, 1))) ?>
        </div>
    </div>

    <button class="menu-toggle"
            type="button"
            aria-label="Buka navigasi"
            data-menu-toggle>
        ☰
    </button>
</header>

<?php $role = $user['role'] ?? ''; $sidebar_file = __DIR__ . '/sidebar_' . $role . '.php'; if (file_exists($sidebar_file)) require_once $sidebar_file; ?>

<main class="main-content">

<?php if ($flash): ?>
    <div class="alert alert-<?= e($flash['type']) ?>"
         role="status">
        <?= e($flash['message']) ?>
    </div>
<?php endif; ?>
