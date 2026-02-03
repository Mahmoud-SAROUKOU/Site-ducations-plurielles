<?php

/**
 * API - Authentification
 */

// Démarrer la session AVANT tout
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// Récupérer les données
$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    $input = $_POST;
}

require_once __DIR__ . '/../auth.php';

$action = $input['action'] ?? '';

// Créer instance Auth SANS valider la session (pour permettre login/check/logout)
$auth = new Auth(true);

switch ($action) {
    case 'login':
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';
        $skipPassword = $input['skip_password'] ?? false;

        if (empty($email)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => '❌ Email requis'
            ]);
            exit;
        }

        // Si skip_password est true, ne pas vérifier le mot de passe
        if ($skipPassword === true || $skipPassword === 'true') {
            $result = $auth->loginWithoutPassword($email);
        } else {
            $result = $auth->login($email, $password);
        }

        if (is_array($result) && isset($result['success']) && $result['success']) {
            $admin = $auth->getAdmin();
            if ($admin) {
                echo json_encode([
                    'success' => true,
                    'message' => $result['message'] ?? $result['msg'] ?? '✅ Connexion réussie',
                    'admin' => [
                        'id' => $admin['id'],
                        'nom' => $admin['nom'],
                        'email' => $admin['email'],
                        'role' => $admin['role'],
                        'actif' => $admin['actif']
                    ],
                    'token' => $_SESSION['token'] ?? null
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => '❌ Erreur de récupération des données admin'
                ]);
            }
        } else {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => $result['message'] ?? $result['msg'] ?? '❌ Identifiants invalides'
            ]);
        }
        break;

    case 'check':
        if ($auth->isConnected()) {
            $admin = $auth->getAdmin();
            echo json_encode([
                'success' => true,
                'connected' => true,
                'admin' => [
                    'id' => $admin['id'],
                    'nom' => $admin['nom'],
                    'email' => $admin['email'],
                    'role' => $admin['role'],
                    'actif' => $admin['actif']
                ]
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'connected' => false
            ]);
        }
        break;

    case 'logout':
        $auth->logout();
        echo json_encode([
            'success' => true,
            'message' => 'Déconnexion réussie'
        ]);
        break;

    default:
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Action invalide'
        ]);
}
