<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
requireAdmin();

$pageTitle = 'Админка: комментарии';

$stmt = $pdo->query(
    'SELECT comments.id, comments.content, comments.created_at, users.name AS author_name, posts.title AS post_title
     FROM comments
     JOIN users ON users.id = comments.user_id
     JOIN posts ON posts.id = comments.post_id
     ORDER BY comments.created_at DESC
     LIMIT 50'
);
$comments = $stmt->fetchAll();

require __DIR__ . '/../templates/header.php';
?>
<section>
    <h1>Последние комментарии</h1>
    <a class="button-link" href="<?= e(appUrl('admin/posts.php')) ?>">К постам</a>
    <table>
        <thead>
            <tr><th>ID</th><th>Пост</th><th>Автор</th><th>Дата</th><th>Текст</th><th></th></tr>
        </thead>
        <tbody>
        <?php foreach ($comments as $comment): ?>
            <tr>
                <td><?= (int) $comment['id'] ?></td>
                <td><?= e($comment['post_title']) ?></td>
                <td><?= e($comment['author_name']) ?></td>
                <td><?= e($comment['created_at']) ?></td>
                <td><?= e(excerpt($comment['content'], 120)) ?></td>
                <td>
                    <form method="post" action="<?= e(appUrl('admin/delete_comment.php')) ?>" onsubmit="return confirm('Удалить комментарий?');">
                        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                        <input type="hidden" name="id" value="<?= (int) $comment['id'] ?>">
                        <button type="submit">Удалить</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php require __DIR__ . '/../templates/footer.php'; ?>
