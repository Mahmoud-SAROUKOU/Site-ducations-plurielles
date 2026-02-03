<?php

/**
 * HOSTINGER - UPLOAD D'IMAGES
 * Uploadez ce fichier sur votre hébergement (ex: /admin/api/upload.php)
 * Configurez l'URL dans admin.html > Paramètres > Synchronisation
 */

// ====== CONFIGURATION ======
define('ADMIN_SYNC_KEY', 'change_me');

define('UPLOAD_DIR', __DIR__ . '/uploads/images');
// URL publique vers le dossier uploads/images (à ajuster)
define('UPLOAD_BASE_URL', 'https://votre-domaine.com/uploads/images');
// Taille max (octets) et dimensions max
define('MAX_UPLOAD_BYTES', 5 * 1024 * 1024); // 5MB
define('MAX_WIDTH', 1600);
define('MAX_HEIGHT', 1600);
define('JPEG_QUALITY', 82);
define('WEBP_QUALITY', 80);
define('PNG_COMPRESSION', 6);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: X-Admin-Sync-Key, Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
    exit;
}

$providedKey = $_SERVER['HTTP_X_ADMIN_SYNC_KEY'] ?? '';
if (!ADMIN_SYNC_KEY || $providedKey !== ADMIN_SYNC_KEY) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Clé de synchronisation invalide']);
    exit;
}

// Suppression d'image
$action = trim($_POST['action'] ?? 'upload');
if ($action === 'delete') {
    $url = trim($_POST['url'] ?? '');
    if ($url === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'URL manquante']);
        exit;
    }

    $base = rtrim(UPLOAD_BASE_URL, '/') . '/';
    if (strpos($url, $base) !== 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'URL non autorisée']);
        exit;
    }

    $filename = basename(parse_url($url, PHP_URL_PATH));
    $path = rtrim(UPLOAD_DIR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

    if (is_file($path)) {
        unlink($path);
        echo json_encode(['success' => true, 'deleted' => true]);
        exit;
    }

    echo json_encode(['success' => true, 'deleted' => false]);
    exit;
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Fichier manquant ou invalide']);
    exit;
}

if ($_FILES['file']['size'] > MAX_UPLOAD_BYTES) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Fichier trop volumineux']);
    exit;
}

$allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
$mime = mime_content_type($_FILES['file']['tmp_name']);
if (!isset($allowed[$mime])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Type de fichier non autorisé']);
    exit;
}

if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

$ext = $allowed[$mime];
$base = pathinfo($_FILES['file']['name'], PATHINFO_FILENAME);
$base = preg_replace('/[^a-zA-Z0-9_-]+/', '-', $base);
$base = trim($base, '-');
if ($base === '') {
    $base = 'image';
}

$filename = $base . '-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
$dest = rtrim(UPLOAD_DIR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

$tmp = $_FILES['file']['tmp_name'];
$imageInfo = getimagesize($tmp);
if (!$imageInfo) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Image invalide']);
    exit;
}

[$width, $height] = $imageInfo;
$scale = min(MAX_WIDTH / $width, MAX_HEIGHT / $height, 1);
$newWidth = (int)floor($width * $scale);
$newHeight = (int)floor($height * $scale);

switch ($mime) {
    case 'image/jpeg':
        $src = imagecreatefromjpeg($tmp);
        break;
    case 'image/png':
        $src = imagecreatefrompng($tmp);
        break;
    case 'image/webp':
        $src = imagecreatefromwebp($tmp);
        break;
    case 'image/gif':
        $src = imagecreatefromgif($tmp);
        break;
    default:
        $src = null;
}

if (!$src) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Image non supportée']);
    exit;
}

$dst = imagecreatetruecolor($newWidth, $newHeight);

if (in_array($mime, ['image/png', 'image/webp', 'image/gif'], true)) {
    imagealphablending($dst, false);
    imagesavealpha($dst, true);
    $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
    imagefilledrectangle($dst, 0, 0, $newWidth, $newHeight, $transparent);
}

imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

$saved = false;
switch ($mime) {
    case 'image/jpeg':
        $saved = imagejpeg($dst, $dest, JPEG_QUALITY);
        break;
    case 'image/png':
        $saved = imagepng($dst, $dest, PNG_COMPRESSION);
        break;
    case 'image/webp':
        $saved = imagewebp($dst, $dest, WEBP_QUALITY);
        break;
    case 'image/gif':
        $saved = imagegif($dst, $dest);
        break;
}

imagedestroy($src);
imagedestroy($dst);

if (!$saved) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Upload échoué']);
    exit;
}

$url = rtrim(UPLOAD_BASE_URL, '/') . '/' . $filename;

echo json_encode(['success' => true, 'url' => $url]);
