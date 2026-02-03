<?php

/**
 * API - Vérifier si des administrateurs existent
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../db.php';

try {
    $pdo = Database::connect();

    // Vérifier si au moins un admin existe
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM admins");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $exists = $result['count'] > 0;

    echo json_encode([
        'success' => true,
        'exists' => $exists,
        'count' => (int)$result['count']
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'exists' => false,
        'error' => $e->getMessage()
    ]);
}
