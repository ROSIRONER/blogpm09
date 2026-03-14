<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
requireAdmin();

$pageTitle = 'Админка: посты';

$stmt = $pdo->query(
    'SELECT posts.id, posts.title, posts.created_at, users.name AS author_name
     FROM posts
     JOIN users ON users.id = posts.user_id
     ORDER BY posts.created_at DESC'
);
$posts = $stmt->fetchAll();

require __DIR__ . '/../templates/header.php';
?>
<section>
    <h1>Управление постами</h1>
    <div class="admin-links">
        <a class="button-link" href="<?= e(appUrl('admin/post_form.php')) ?>">Добавить пост</a>
        <a class="button-link" href="<?= e(appUrl('admin/comments.php')) ?>">Комментарии</a>
    </div>
    <table>
        <thead>
        <tr><th>ID</th><th>Заголовок</th><th>Автор</th><th>Дата</th><th>Действия</th></tr>
        </thead>
        <tbody>
        <?php foreach ($posts as $post): ?>
            <tr>
                <td><?= (int) $post['id'] ?></td>
                <td><?= e($post['title']) ?></td>
                <td><?= e($post['author_name']) ?></td>
                <td><?= e($post['created_at']) ?></td>
                <td>
                    <a href="<?= e(appUrl('admin/post_form.php')) ?>?id=<?= (int) $post['id'] ?>">Редактировать</a>
                    <form class="inline" method="post" action="<?= e(appUrl('admin/delete_post.php')) ?>" onsubmit="return confirm('Удалить пост?');">
                        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                        <input type="hidden" name="id" value="<?= (int) $post['id'] ?>">
                        <button type="submit">Удалить</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php require __DIR__ . '/../templates/footer.php'; ?>
