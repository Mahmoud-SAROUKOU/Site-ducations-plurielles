<?php

/**
 * API - Gestion des publicités
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../auth.php';

$pdo = Database::connect();
$auth = new Auth(true);

// GET - Liste des publicités actives
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->query("
            SELECT id, titre, description, lien, budget, statut, date_debut, date_fin, created_at 
            FROM publicites 
            WHERE statut = 'active' AND date_fin >= date('now') 
            ORDER BY date_debut DESC
        ");
        $ads = $stmt->fetchAll();

        echo json_encode([
            'success' => true,
            'ads' => $ads
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erreur serveur: ' . $e->getMessage()
        ]);
    }
    exit;
}

// POST - Créer/Modifier/Supprimer des publicités (nécessite authentification)
$auth2 = new Auth();
if (!$auth2->isConnected()) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Non authentifié'
    ]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    $input = $_POST;
}

$action = $input['action'] ?? '';

switch ($action) {
    case 'create':
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO publicites (titre, description, lien, budget, statut, date_debut, date_fin, admin_id) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $input['titre'] ?? '',
                $input['description'] ?? '',
                $input['lien'] ?? '',
                $input['budget'] ?? 0,
                $input['statut'] ?? 'inactive',
                $input['date_debut'] ?? date('Y-m-d'),
                $input['date_fin'] ?? date('Y-m-d', time() + 30 * 86400),
                $auth2->getAdmin()['id'] ?? null
            ]);

            echo json_encode([
                'success' => true,
                'message' => '✅ Publicité créée',
                'id' => $pdo->lastInsertId()
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur création: ' . $e->getMessage()
            ]);
        }
        break;

    case 'update':
        try {
            $stmt = $pdo->prepare(
                "UPDATE publicites 
                 SET titre = ?, description = ?, lien = ?, budget = ?, statut = ?, date_fin = ? 
                 WHERE id = ?"
            );
            $stmt->execute([
                $input['titre'] ?? '',
                $input['description'] ?? '',
                $input['lien'] ?? '',
                $input['budget'] ?? 0,
                $input['statut'] ?? 'inactive',
                $input['date_fin'] ?? date('Y-m-d', time() + 30 * 86400),
                $input['id'] ?? 0
            ]);

            echo json_encode([
                'success' => true,
                'message' => '✅ Publicité mise à jour'
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur mise à jour: ' . $e->getMessage()
            ]);
        }
        break;

    case 'delete':
        try {
            $stmt = $pdo->prepare("DELETE FROM publicites WHERE id = ?");
            $stmt->execute([$input['id'] ?? 0]);

            echo json_encode([
                'success' => true,
                'message' => '✅ Publicité supprimée'
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur suppression: ' . $e->getMessage()
            ]);
        }
        break;

    case 'list-all':
        try {
            $stmt = $pdo->query("
                SELECT id, titre, description, lien, budget, statut, date_debut, date_fin, created_at 
                FROM publicites 
                ORDER BY created_at DESC
            ");
            $ads = $stmt->fetchAll();

            echo json_encode([
                'success' => true,
                'ads' => $ads
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ]);
        }
        break;

    default:
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Action invalide'
        ]);
}
