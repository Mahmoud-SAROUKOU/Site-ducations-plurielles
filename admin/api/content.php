<?php
require_once __DIR__ . '/../functions.php';

header('Content-Type: application/json; charset=utf-8');

$articlesStmt = db()->query("SELECT a.*, u.name as author_name FROM articles a JOIN users u ON u.id = a.author_id WHERE a.status = 'published' AND (a.published_at IS NULL OR a.published_at <= NOW()) ORDER BY COALESCE(a.published_at, a.created_at) DESC");
$articles = [];
foreach ($articlesStmt->fetchAll() as $row) {
    $tags = [];
    if (!empty($row['tags'])) {
        $tags = array_values(array_filter(array_map('trim', explode(',', $row['tags']))));
    }
    $articles[] = [
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
    ];
}

$adsStmt = db()->query("SELECT * FROM ads WHERE status = 'active' ORDER BY display_order ASC, created_at DESC");
$ads = [];
foreach ($adsStmt->fetchAll() as $row) {
    $ads[] = [
        'id' => (int)$row['id'],
        'name' => $row['name'],
        'message' => $row['message'] ?: $row['name'],
        'icon' => $row['icon'] ?: '📢',
        'order' => (int)($row['display_order'] ?? 0),
        'position' => $row['position'],
        'image' => $row['image_url'],
        'target' => $row['target_url'],
        'active' => true
    ];
}

echo json_encode([
    'articles' => $articles,
    'ads' => $ads,
    'lastUpdate' => date('c')
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
