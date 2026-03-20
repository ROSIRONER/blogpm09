<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
requireAdmin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$isEdit = $id > 0;
$errors = [];

$post = [
    'title' => '',
    'content' => '',
    'image_path' => '',
];

if ($isEdit) {
    $selectStmt = $pdo->prepare('SELECT id, title, content, image_path FROM posts WHERE id = :id LIMIT 1');
    $selectStmt->execute(['id' => $id]);
    $existing = $selectStmt->fetch();
    if (!$existing) {
        http_response_code(404);
        exit('Пост не найден');
    }
    $post = $existing;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Неверный CSRF токен.';
    }

    $title = trim((string) ($_POST['title'] ?? ''));
    $content = trim((string) ($_POST['content'] ?? ''));
    $imagePath = $post['image_path'];

    if ($title === '') {
        $errors[] = 'Введите заголовок.';
    }
    if ($content === '') {
        $errors[] = 'Введите текст.';
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Ошибка загрузки файла.';
        } else {
            $mime = mime_content_type($_FILES['image']['tmp_name']);
            $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
            if (!isset($allowed[$mime])) {
                $errors[] = 'Разрешены только JPG, PNG, WEBP.';
            } else {
                $filename = bin2hex(random_bytes(10)) . '.' . $allowed[$mime];
                $destination = __DIR__ . '/../uploads/' . $filename;
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                    $errors[] = 'Не удалось сохранить изображение.';
                } else {
                    $imagePath = 'uploads/' . $filename;
                }
            }
        }
    }

    if (!$errors) {
        if ($isEdit) {
            $updateStmt = $pdo->prepare(
                'UPDATE posts SET title = :title, content = :content, image_path = :image_path WHERE id = :id'
            );
            $updateStmt->execute([
                'title' => $title,
                'content' => $content,
                'image_path' => $imagePath,
                'id' => $id,
            ]);
        } else {
            $user = currentUser();
            $insertStmt = $pdo->prepare(
                'INSERT INTO posts (user_id, title, content, image_path, created_at)
                 VALUES (:user_id, :title, :content, :image_path, NOW())'
            );
            $insertStmt->execute([
                'user_id' => $user['id'],
                'title' => $title,
                'content' => $content,
                'image_path' => $imagePath,
            ]);
        }

        redirect('/admin/posts.php');
    }

    $post['title'] = $title;
    $post['content'] = $content;
    $post['image_path'] = $imagePath;
}

$pageTitle = $isEdit ? 'Редактирование поста' : 'Новый пост';
require __DIR__ . '/../templates/header.php';
?>
<section class="auth-box">
    <h1><?= $isEdit ? 'Редактировать пост' : 'Добавить пост' ?></h1>

    <?php foreach ($errors as $error): ?>
        <div class="alert error"><?= e($error) ?></div>
    <?php endforeach; ?>

    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <label>Заголовок <input type="text" name="title" value="<?= e($post['title']) ?>" required></label>
        <label>Текст <textarea name="content" rows="10" required><?= e($post['content']) ?></textarea></label>
        <label>Картинка <input type="file" name="image" accept="image/png,image/jpeg,image/webp"></label>
        <?php if (!empty($post['image_path'])): ?>
            <img class="thumb" src="<?= e(appUrl($post['image_path'])) ?>" alt="Текущая картинка">
        <?php endif; ?>
        <button type="submit">Сохранить</button>
    </form>
</section>
<?php require __DIR__ . '/../templates/footer.php'; ?>
