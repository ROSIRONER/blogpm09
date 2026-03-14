<?php

declare(strict_types=1);

require_once __DIR__ . '/config/bootstrap.php';

$pageTitle = 'Регистрация';
$errors = [];
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $passwordConfirm = (string) ($_POST['password_confirm'] ?? '');

    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Неверный CSRF токен.';
    }
    if ($name === '' || mb_strlen($name) < 2) {
        $errors[] = 'Введите имя (минимум 2 символа).';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Введите корректный email.';
    }
    if (mb_strlen($password) < 6) {
        $errors[] = 'Пароль должен быть не менее 6 символов.';
    }
    if ($password !== $passwordConfirm) {
        $errors[] = 'Пароли не совпадают.';
    }

    if (!$errors) {
        $checkStmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
        $checkStmt->execute(['email' => $email]);
        if ($checkStmt->fetch()) {
            $errors[] = 'Пользователь с таким email уже существует.';
        }
    }

    if (!$errors) {
        $insertStmt = $pdo->prepare(
            'INSERT INTO users (name, email, password_hash, role, created_at)
             VALUES (:name, :email, :password_hash, :role, NOW())'
        );
        $insertStmt->execute([
            'name' => $name,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'user',
        ]);

        redirect('/login.php');
    }
}

require __DIR__ . '/templates/header.php';
?>
<section class="auth-box">
    <h1>Регистрация</h1>
    <?php foreach ($errors as $error): ?>
        <div class="alert error"><?= e($error) ?></div>
    <?php endforeach; ?>

    <form method="post" action="<?= e(appUrl('register.php')) ?>">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <label>Имя <input type="text" name="name" value="<?= e($name) ?>" required></label>
        <label>Email <input type="email" name="email" value="<?= e($email) ?>" required></label>
        <label>Пароль <input type="password" name="password" required></label>
        <label>Подтверждение пароля <input type="password" name="password_confirm" required></label>
        <button type="submit">Зарегистрироваться</button>
    </form>
</section>
<?php require __DIR__ . '/templates/footer.php'; ?>
