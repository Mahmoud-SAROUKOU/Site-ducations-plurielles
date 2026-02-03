<?php
// Minimal config loader for .env
$envPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        if (!str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        if (!isset($_ENV[$key])) {
            $_ENV[$key] = $value;
        }
    }
}

define('APP_URL', $_ENV['APP_URL'] ?? 'http://localhost');
define('APP_NAME', $_ENV['APP_NAME'] ?? 'Admin');
define('ADMIN_SYNC_KEY', $_ENV['ADMIN_SYNC_KEY'] ?? '');

define('USE_SQLITE', $_ENV['USE_SQLITE'] ?? true);
define('SQLITE_PATH', __DIR__ . '/database.sqlite');

define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'educations_plurielles');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');

define('MAIL_FROM', $_ENV['MAIL_FROM'] ?? 'noreply@educations-plurielles.fr');
define('MAIL_FROM_NAME', $_ENV['MAIL_FROM_NAME'] ?? 'Éducations Plurielles');

define('MAIL_SMTP_HOST', $_ENV['MAIL_SMTP_HOST'] ?? '');
define('MAIL_SMTP_PORT', $_ENV['MAIL_SMTP_PORT'] ?? '587');
define('MAIL_SMTP_USER', $_ENV['MAIL_SMTP_USER'] ?? '');
define('MAIL_SMTP_PASS', $_ENV['MAIL_SMTP_PASS'] ?? '');
define('MAIL_SMTP_SECURE', $_ENV['MAIL_SMTP_SECURE'] ?? 'tls');

function db(): PDO
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    if (USE_SQLITE) {
        $dsn = 'sqlite:' . SQLITE_PATH;
        $pdo = new PDO($dsn, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $pdo->exec('PRAGMA foreign_keys = ON');
    } else {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}
