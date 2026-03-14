<?php

declare(strict_types=1);

require_once __DIR__ . '/config/bootstrap.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    http_response_code(404);
    exit('Пост не найден');
}

$postStmt = $pdo->prepare(
    'SELECT posts.id, posts.title, posts.content, posts.image_path, posts.created_at, users.name AS author_name
     FROM posts
     JOIN users ON users.id = posts.user_id
     WHERE posts.id = :id
     LIMIT 1'
);
$postStmt->execute(['id' => $id]);
$post = $postStmt->fetch();

if (!$post) {
    http_response_code(404);
    exit('Пост не найден');
}

$commentsStmt = $pdo->prepare(
    'SELECT comments.id, comments.content, comments.created_at, users.name AS author_name
     FROM comments
     JOIN users ON users.id = comments.user_id
     WHERE comments.post_id = :post_id
     ORDER BY comments.created_at DESC'
);
$commentsStmt->execute(['post_id' => $id]);
$comments = $commentsStmt->fetchAll();

$pageTitle = $post['title'];
$user = currentUser();

require __DIR__ . '/templates/header.php';
?>
<article class="post-full">
    <h1><?= e($post['title']) ?></h1>
    <div class="meta">
        <span>Автор: <?= e($post['author_name']) ?></span>
        <span><?= e($post['created_at']) ?></span>
    </div>
    <?php if (!empty($post['image_path'])): ?>
        <img class="cover" src="<?= e(appUrl($post['image_path'])) ?>" alt="Изображение поста">
    <?php endif; ?>
    <div class="content"><?= nl2br(e($post['content'])) ?></div>
    <button class="like-button" data-like-key="post-<?= (int) $post['id'] ?>" data-label="поста">❤️ <span>0</span></button>
</article>

<section class="comments" data-post-id="<?= (int) $post['id'] ?>">
    <h2>Комментарии</h2>

    <?php if ($user): ?>
        <form id="comment-form">
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
            <textarea name="content" rows="4" required placeholder="Оставьте комментарий"></textarea>
            <button type="submit">Отправить</button>
        </form>
    <?php else: ?>
        <p>Чтобы оставить комментарий, войдите.</p>
    <?php endif; ?>

    <div id="comment-list">
        <?php foreach ($comments as $comment): ?>
            <article class="comment">
                <div class="meta">
                    <span><?= e($comment['author_name']) ?></span>
                    <span><?= e($comment['created_at']) ?></span>
                </div>
                <p><?= nl2br(e($comment['content'])) ?></p>
                <button class="like-button" data-like-key="comment-<?= (int) $comment['id'] ?>" data-label="комментария">👍 <span>0</span></button>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php require __DIR__ . '/templates/footer.php'; ?>
