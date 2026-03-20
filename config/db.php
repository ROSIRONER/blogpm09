<?php

declare(strict_types=1);

$host = getenv('DB_HOST') ?: '127.0.0.1';
$dbName = getenv('DB_NAME') ?: 'blog';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') ?: '';
$charset = 'utf8mb4';

$localConfigPath = __DIR__ . '/db.credentials.php';
if (is_file($localConfigPath)) {
    /** @var array{host?:string,name?:string,user?:string,pass?:string,charset?:string} $dbLocalConfig */
    $dbLocalConfig = require $localConfigPath;
    $host = $dbLocalConfig['host'] ?? $host;
    $dbName = $dbLocalConfig['name'] ?? $dbName;
    $dbUser = $dbLocalConfig['user'] ?? $dbUser;
    $dbPass = $dbLocalConfig['pass'] ?? $dbPass;
    $charset = $dbLocalConfig['charset'] ?? $charset;
}

$host = preg_replace('#^https?://#', '', trim((string) $host));
$host = rtrim((string) $host, '/');
$charset = trim((string) $charset) !== '' ? (string) $charset : 'utf8mb4';

$dsn = "mysql:host={$host};dbname={$dbName};charset={$charset}";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, (string) $dbUser, (string) $dbPass, $options);
} catch (PDOException $exception) {
    error_log('[DB] Connection failed: ' . $exception->getMessage());
    http_response_code(500);
    exit('Database connection failed. Проверьте параметры DB_HOST/DB_NAME/DB_USER/DB_PASS в config/db.credentials.php или окружении.');
}
