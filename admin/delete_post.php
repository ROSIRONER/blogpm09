<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    http_response_code(422);
    exit('Некорректный запрос');
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
if ($id <= 0) {
    redirect('/admin/posts.php');
}

$pdo->beginTransaction();
try {
    $deleteComments = $pdo->prepare('DELETE FROM comments WHERE post_id = :post_id');
    $deleteComments->execute(['post_id' => $id]);

    $deletePost = $pdo->prepare('DELETE FROM posts WHERE id = :id');
    $deletePost->execute(['id' => $id]);

    $pdo->commit();
} catch (Throwable $throwable) {
    $pdo->rollBack();
}

redirect('/admin/posts.php');
