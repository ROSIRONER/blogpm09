<?php

declare(strict_types=1);

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function startsWith(string $haystack, string $needle): bool
{
    if ($needle === '') {
        return true;
    }

    if (function_exists('str_starts_with')) {
        return str_starts_with($haystack, $needle);
    }

    return substr($haystack, 0, strlen($needle)) === $needle;
}

function strLenSafe(string $text): int
{
    return function_exists('mb_strlen') ? mb_strlen($text) : strlen($text);
}

function strSubSafe(string $text, int $start, int $length): string
{
    return function_exists('mb_substr') ? mb_substr($text, $start, $length) : substr($text, $start, $length);
}

function appBaseUrl(): string
{
    static $baseUrl;
    if ($baseUrl !== null) {
        return $baseUrl;
    }

    $appRoot = realpath(__DIR__ . '/..');
    $docRoot = isset($_SERVER['DOCUMENT_ROOT']) ? realpath((string) $_SERVER['DOCUMENT_ROOT']) : false;

    if ($appRoot && $docRoot && startsWith($appRoot, $docRoot)) {
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



function assetUrl(string $path): string
{
    $relativePath = ltrim($path, '/');
    $filePath = realpath(__DIR__ . '/../' . $relativePath);
    $url = appUrl($relativePath);

    if ($filePath && is_file($filePath)) {
        return $url . '?v=' . filemtime($filePath);
    }

    return $url;
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
    if (startsWith($path, 'http://') || startsWith($path, 'https://')) {
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
    if (strLenSafe($text) <= $length) {
        return $text;
    }

    return strSubSafe($text, 0, $length) . '...';
}
