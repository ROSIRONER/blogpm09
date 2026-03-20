<?php

declare(strict_types=1);

require_once __DIR__ . '/config/bootstrap.php';

$pageTitle = 'Лента постов';
$perPage = 5;
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$offset = ($page - 1) * $perPage;

$totalStmt = $pdo->query('SELECT COUNT(*) FROM posts');
$totalPosts = (int) $totalStmt->fetchColumn();
$totalPages = max(1, (int) ceil($totalPosts / $perPage));

$listStmt = $pdo->prepare(
    'SELECT posts.id, posts.title, posts.content, posts.image_path, posts.created_at, users.name AS author_name
     FROM posts
     JOIN users ON users.id = posts.user_id
     ORDER BY posts.created_at DESC
     LIMIT :limit OFFSET :offset'
);
$listStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$listStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$listStmt->execute();
$posts = $listStmt->fetchAll();

require __DIR__ . '/templates/header.php';
?>
<section>
    <h1>Свежие статьи</h1>
    <?php if (!$posts): ?>
        <p>Пока нет постов.</p>
    <?php endif; ?>

    <div class="post-grid">
    <?php foreach ($posts as $post): ?>
        <article class="card post-card">
            <?php if (!empty($post['image_path'])): ?>
                <img class="cover" src="<?= e(appUrl($post['image_path'])) ?>" alt="Изображение поста">
            <?php endif; ?>
            <h2><a href="<?= e(appUrl('post.php')) ?>?id=<?= (int) $post['id'] ?>"><?= e($post['title']) ?></a></h2>
            <p><?= nl2br(e(excerpt($post['content'], 200))) ?></p>
            <div class="meta">
                <span>Автор: <?= e($post['author_name']) ?></span>
                <span><?= e($post['created_at']) ?></span>
            </div>
            <button class="like-button" data-like-key="post-<?= (int) $post['id'] ?>" data-label="поста">❤️ <span>0</span></button>
        </article>
    <?php endforeach; ?>
    </div>

    <nav class="pagination">
        <?php if ($page > 1): ?>
            <a href="<?= e(appUrl('index.php')) ?>?page=<?= $page - 1 ?>">Предыдущая</a>
        <?php endif; ?>
        <span>Страница <?= $page ?> из <?= $totalPages ?></span>
        <?php if ($page < $totalPages): ?>
            <a href="<?= e(appUrl('index.php')) ?>?page=<?= $page + 1 ?>">Следующая</a>
        <?php endif; ?>
    </nav>
</section>
<?php require __DIR__ . '/templates/footer.php'; ?>
