<?php

declare(strict_types=1);

require_once __DIR__ . '/config/bootstrap.php';

$pageTitle = 'Авторизация';
$errors = [];
$login = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim((string) ($_POST['login'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Неверный CSRF токен.';
    }

    if ($login === '' || $password === '') {
        $errors[] = 'Заполните все поля.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT id, name, email, password_hash, role FROM users WHERE email = :email_login OR name = :name_login LIMIT 1');
        $stmt->execute(['email_login' => $login, 'name_login' => $login]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $errors[] = 'Неверный логин/email или пароль.';
        } else {
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            redirect('/index.php');
        }
    }
}

require __DIR__ . '/templates/header.php';
?>
<section class="auth-box">
    <h1>Авторизация</h1>
    <?php foreach ($errors as $error): ?>
        <div class="alert error"><?= e($error) ?></div>
    <?php endforeach; ?>
    <form method="post" action="<?= e(appUrl('login.php')) ?>">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <label>Логин или Email <input type="text" name="login" value="<?= e($login) ?>" required></label>
        <label>Пароль <input type="password" name="password" required></label>
        <button type="submit">Войти</button>
    </form>
</section>
<?php require __DIR__ . '/templates/footer.php'; ?>
