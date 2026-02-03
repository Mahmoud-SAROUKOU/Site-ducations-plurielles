<?php

/**
 * API - Stats
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../auth.php';

$auth = new Auth();
$auth->require();

$pdo = Database::connect();

try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM admins WHERE actif = 1");
    $admin_count = $stmt->fetch()['count'] ?? 0;

    // Articles, Ads, Logs si les tables existent
    $article_count = 0;
    $ad_count = 0;
    $log_count = 0;

    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM articles");
        $article_count = $stmt->fetch()['count'] ?? 0;
    } catch (Exception $e) {
    }

    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM ads");
        $ad_count = $stmt->fetch()['count'] ?? 0;
    } catch (Exception $e) {
    }

    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM logs");
        $log_count = $stmt->fetch()['count'] ?? 0;
    } catch (Exception $e) {
    }

    echo json_encode([
        'admin_count' => $admin_count,
        'article_count' => $article_count,
        'ad_count' => $ad_count,
        'log_count' => $log_count
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur']);
}
