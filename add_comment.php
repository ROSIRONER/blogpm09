<?php

declare(strict_types=1);

require_once __DIR__ . '/config/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

if (!isAuthenticated()) {
    http_response_code(401);
    echo json_encode(['error' => 'Требуется авторизация']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Метод не поддерживается']);
    exit;
}

if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    http_response_code(422);
    echo json_encode(['error' => 'Неверный CSRF токен']);
    exit;
}

$postId = isset($_POST['post_id']) ? (int) $_POST['post_id'] : 0;
$content = trim((string) ($_POST['content'] ?? ''));

if ($postId <= 0 || $content === '') {
    http_response_code(422);
    echo json_encode(['error' => 'Невалидные данные']);
    exit;
}

$postCheck = $pdo->prepare('SELECT id FROM posts WHERE id = :id LIMIT 1');
$postCheck->execute(['id' => $postId]);
if (!$postCheck->fetch()) {
    http_response_code(404);
    echo json_encode(['error' => 'Пост не найден']);
    exit;
}

$user = currentUser();
$insertStmt = $pdo->prepare(
    'INSERT INTO comments (post_id, user_id, content, created_at)
     VALUES (:post_id, :user_id, :content, NOW())'
);
$insertStmt->execute([
    'post_id' => $postId,
    'user_id' => $user['id'],
    'content' => $content,
]);

$createdAt = date('Y-m-d H:i:s');

echo json_encode([
    'id' => (int) $pdo->lastInsertId(),
    'author' => $user['name'],
    'content' => $content,
    'created_at' => $createdAt,
]);
