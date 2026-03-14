<?php

declare(strict_types=1);

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function appBaseUrl(): string
{
    static $baseUrl;
    if ($baseUrl !== null) {
        return $baseUrl;
    }

    $appRoot = realpath(__DIR__ . '/..');
    $docRoot = isset($_SERVER['DOCUMENT_ROOT']) ? realpath((string) $_SERVER['DOCUMENT_ROOT']) : false;

    if ($appRoot && $docRoot && str_starts_with($appRoot, $docRoot)) {
        $relative = str_replace('\\', '/', substr($appRoot, strlen($docRoot)) ?: '');
        $relative = '/' . trim($relative, '/');
        $baseUrl = $relative === '/' ? '' : $relative;
    } else {
        $baseUrl = '';
    }

    return $baseUrl;
}

function appUrl(string $path = ''): string
{
    $base = appBaseUrl();
    $cleanPath = ltrim($path, '/');

    if ($cleanPath === '') {
        return $base !== '' ? $base : '/';
    }

    return ($base !== '' ? $base : '') . '/' . $cleanPath;
}

function currentUser(): ?array
{
    if (!isset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['role'])) {
        return null;
    }

    return [
        'id' => (int) $_SESSION['user_id'],
        'name' => (string) $_SESSION['user_name'],
        'role' => (string) $_SESSION['role'],
    ];
}

function isAuthenticated(): bool
{
    return currentUser() !== null;
}

function isAdmin(): bool
{
    $user = currentUser();
    return $user !== null && $user['role'] === 'admin';
}

function redirect(string $path): void
{
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        header('Location: ' . $path);
    } else {
        header('Location: ' . appUrl($path));
    }
    exit;
}

function requireAdmin(): void
{
    if (!isAdmin()) {
        http_response_code(403);
        exit('Access denied');
    }
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verifyCsrfToken(?string $token): bool
{
    return isset($_SESSION['csrf_token']) && is_string($token) && hash_equals($_SESSION['csrf_token'], $token);
}

function excerpt(string $text, int $length = 200): string
{
    if (mb_strlen($text) <= $length) {
        return $text;
    }

    return mb_substr($text, 0, $length) . '...';
}
