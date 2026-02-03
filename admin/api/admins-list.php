<?php

/**
 * API - Liste des administrateurs
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../auth.php';

$auth = new Auth();
$auth->require();

$pdo = Database::connect();

try {
    $stmt = $pdo->query("SELECT id, nom, email, role, actif FROM admins ORDER BY created_at DESC");
    $admins = $stmt->fetchAll();
    echo json_encode($admins);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur']);
}
