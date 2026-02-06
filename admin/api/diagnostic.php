<?php

/**
 * Diagnostic serveur (Hostinger)
 * Accès protégé par ADMIN_SYNC_KEY
 */

require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Admin-Sync-Key');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$providedKey = $_GET['key'] ?? ($_SERVER['HTTP_X_ADMIN_SYNC_KEY'] ?? '');
if (!defined('ADMIN_SYNC_KEY') || ADMIN_SYNC_KEY === '' || $providedKey !== ADMIN_SYNC_KEY) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Clé de diagnostic invalide'
    ]);
    exit;
}

$response = [
    'success' => true,
    'timestamp' => date('c'),
    'php_version' => PHP_VERSION,
    'server' => [
        'host' => $_SERVER['HTTP_HOST'] ?? '',
        'software' => $_SERVER['SERVER_SOFTWARE'] ?? '',
        'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? ''
    ],
    'config' => [
        'app_url' => defined('APP_URL') ? APP_URL : null,
        'use_sqlite' => defined('USE_SQLITE') ? (bool)USE_SQLITE : null,
        'admin_sync_key_set' => defined('ADMIN_SYNC_KEY') ? (ADMIN_SYNC_KEY !== '') : null
    ],
    'db' => [
        'connected' => false,
        'driver' => null,
        'error' => null,
        'tables' => []
    ],
    'uploads' => [
        'dir' => null,
        'exists' => false,
        'writable' => false,
        'base_url' => null
    ],
    'files' => [
        'sync' => file_exists(__DIR__ . '/sync.php'),
        'upload' => file_exists(__DIR__ . '/upload.php'),
        'send_email' => file_exists(__DIR__ . '/send-email.php') || file_exists(__DIR__ . '/send-admin-email.php'),
        'index' => file_exists(__DIR__ . '/index.php')
    ]
];

try {
    $pdo = db();
    $response['db']['connected'] = true;
    $response['db']['driver'] = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

    $tablesToCheck = ['articles', 'ads', 'users', 'admins', 'categories'];
    foreach ($tablesToCheck as $table) {
        try {
            $stmt = $pdo->query("SELECT 1 FROM {$table} LIMIT 1");
            $response['db']['tables'][$table] = true;
        } catch (Exception $e) {
            $response['db']['tables'][$table] = false;
        }
    }
} catch (Exception $e) {
    $response['db']['connected'] = false;
    $response['db']['error'] = $e->getMessage();
}

$uploadDir = $_ENV['UPLOAD_DIR'] ?? (dirname(__DIR__, 2) . '/uploads/images');
$uploadBaseUrl = $_ENV['UPLOAD_BASE_URL'] ?? (defined('APP_URL') ? rtrim(APP_URL, '/') . '/uploads/images' : null);

$response['uploads']['dir'] = $uploadDir;
$response['uploads']['base_url'] = $uploadBaseUrl;
$response['uploads']['exists'] = is_dir($uploadDir);
$response['uploads']['writable'] = is_dir($uploadDir) ? is_writable($uploadDir) : false;

if (!$response['uploads']['exists']) {
    $response['uploads']['warning'] = 'Dossier uploads/images manquant';
}

if ($response['uploads']['exists'] && !$response['uploads']['writable']) {
    $response['uploads']['warning'] = 'Dossier uploads/images non inscriptible';
}

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
