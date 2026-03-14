<?php

declare(strict_types=1);

$user = currentUser();
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' | ' : '' ?>Блог</title>
    <link rel="stylesheet" href="<?= e(assetUrl('assets/css/style.css')) ?>">
</head>
<body>
<header class="site-header">
    <div class="container nav-wrap">
        <a class="brand" href="<?= e(appUrl('index.php')) ?>">Блог</a>
        <button class="menu-toggle" aria-label="Открыть меню">☰</button>
        <nav class="main-nav">
            <a href="<?= e(appUrl('index.php')) ?>">Главная</a>
            <?php if ($user): ?>
                <span class="hello">Привет, <?= e($user['name']) ?></span>
                <?php if ($user['role'] === 'admin'): ?>
                    <a href="<?= e(appUrl('admin/posts.php')) ?>">Админка</a>
                <?php endif; ?>
                <a href="<?= e(appUrl('logout.php')) ?>">Выйти</a>
            <?php else: ?>
                <a href="<?= e(appUrl('login.php')) ?>">Войти</a>
                <a href="<?= e(appUrl('register.php')) ?>">Регистрация</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="container">
