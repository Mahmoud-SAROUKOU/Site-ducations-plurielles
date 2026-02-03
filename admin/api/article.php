<?php
require_once __DIR__ . '/../functions.php';

header('Content-Type: application/json; charset=utf-8');

$slug = trim($_GET['slug'] ?? '');
if ($slug === '') {
    http_response_code(400);
    echo json_encode(['error' => 'slug requis']);
    exit;
}

$stmt = db()->prepare("SELECT a.*, u.name as author_name FROM articles a JOIN users u ON u.id = a.author_id WHERE a.slug = ? AND a.status = 'published' AND (a.published_at IS NULL OR a.published_at <= NOW()) LIMIT 1");
$stmt->execute([$slug]);
$row = $stmt->fetch();
if (!$row) {
    http_response_code(404);
    echo json_encode(['error' => 'article introuvable']);
    exit;
}

$tags = [];
if (!empty($row['tags'])) {
    $tags = array_values(array_filter(array_map('trim', explode(',', $row['tags']))));
}

echo json_encode([
    'id' => (int)$row['id'],
    'title' => $row['title'],
    'slug' => $row['slug'],
    'excerpt' => $row['excerpt'] ?? '',
    'content' => $row['content'],
    'category' => $row['category'] ?? 'parentalite',
    'image' => $row['image_url'] ?? '',
    'tags' => $tags,
    'readTime' => $row['read_time'] ?? '',
    'date' => !empty($row['published_at']) ? date('d F Y', strtotime($row['published_at'])) : date('d F Y', strtotime($row['created_at'])),
    'author' => $row['author_name'] ?? '',
    'createdAt' => $row['created_at'],
    'published' => true
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
