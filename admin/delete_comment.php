<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    http_response_code(422);
    exit('Некорректный запрос');
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
if ($id > 0) {
    $stmt = $pdo->prepare('DELETE FROM comments WHERE id = :id');
    $stmt->execute(['id' => $id]);
}

redirect('/admin/comments.php');
