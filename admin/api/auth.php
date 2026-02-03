<?php

/**
 * ENDPOINT D'AUTHENTIFICATION - Hostinger
 * Gère connexion/déconnexion/vérification session
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Admin-Sync-Key');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Configuration
require_once __DIR__ . '/../config.php';

function db(): PDO
{
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    return $pdo;
}

// Initialiser tables si nécessaire
function initTables()
{
    try {
        $pdo = db();

        // Table users (pour authentification)
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(190) NOT NULL UNIQUE,
                name VARCHAR(120) NOT NULL,
                password_hash VARCHAR(255),
                role ENUM('super-admin', 'admin', 'editor', 'moderator') DEFAULT 'admin',
                status ENUM('active', 'inactive') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                last_login TIMESTAMP NULL,
                INDEX(email),
                INDEX(status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Table sessions
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS sessions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                token VARCHAR(255) NOT NULL UNIQUE,
                ip_address VARCHAR(45),
                user_agent VARCHAR(255),
                expires_at TIMESTAMP NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                INDEX(token),
                INDEX(expires_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Créer super-admin par défaut s'il n'existe pas
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute(['admin@educationsplurielles.local']);
        if (!$stmt->fetch()) {
            $stmt = $pdo->prepare("
                INSERT INTO users (email, name, password_hash, role, status) 
                VALUES (?, ?, NULL, 'super-admin', 'active')
            ");
            $stmt->execute(['admin@educationsplurielles.local', 'Administrateur Principal']);
        }

        return true;
    } catch (Exception $e) {
        error_log("Erreur init tables: " . $e->getMessage());
        return false;
    }
}

// Nettoyer sessions expirées
function cleanExpiredSessions()
{
    try {
        $stmt = db()->prepare("DELETE FROM sessions WHERE expires_at < NOW()");
        $stmt->execute();
    } catch (Exception $e) {
        error_log("Erreur nettoyage sessions: " . $e->getMessage());
    }
}

// Vérifier la clé API
function verifyApiKey()
{
    if (!defined('ADMIN_SYNC_KEY') || empty(ADMIN_SYNC_KEY)) {
        return true; // Pas de clé configurée = pas de vérification
    }

    $providedKey = $_SERVER['HTTP_X_ADMIN_SYNC_KEY'] ?? '';
    return $providedKey === ADMIN_SYNC_KEY;
}

// ACTION: LOGIN
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';

    // Initialiser
    initTables();
    cleanExpiredSessions();

    if ($action === 'login') {
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';

        if (empty($email)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Email requis']);
            exit;
        }

        try {
            $stmt = db()->prepare("SELECT * FROM users WHERE email = ? AND status = 'active'");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user) {
                http_response_code(401);
                echo json_encode(['success' => false, 'error' => 'Email ou mot de passe incorrect']);
                exit;
            }

            // Vérifier mot de passe
            if ($user['role'] === 'super-admin') {
                // Super-admin : email suffit (pas de mot de passe)
                if ($email !== 'admin@educationsplurielles.local') {
                    http_response_code(401);
                    echo json_encode(['success' => false, 'error' => 'Accès non autorisé']);
                    exit;
                }
            } else {
                // Autres : vérifier password_hash
                if (!$user['password_hash'] || !password_verify($password, $user['password_hash'])) {
                    http_response_code(401);
                    echo json_encode(['success' => false, 'error' => 'Email ou mot de passe incorrect']);
                    exit;
                }
            }

            // Créer session
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', time() + 86400); // 24h

            $stmt = db()->prepare("
                INSERT INTO sessions (user_id, token, ip_address, user_agent, expires_at) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $user['id'],
                $token,
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? '',
                $expiresAt
            ]);

            // Mettre à jour last_login
            $stmt = db()->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $stmt->execute([$user['id']]);

            echo json_encode([
                'success' => true,
                'user' => [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'name' => $user['name'],
                    'role' => $user['role']
                ],
                'token' => $token,
                'expiresAt' => strtotime($expiresAt) * 1000 // timestamp JS
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Erreur serveur']);
            error_log("Erreur login: " . $e->getMessage());
        }
        exit;
    }

    if ($action === 'verify') {
        $token = $input['token'] ?? '';

        if (empty($token)) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Token manquant']);
            exit;
        }

        try {
            $stmt = db()->prepare("
                SELECT u.* FROM users u
                JOIN sessions s ON u.id = s.user_id
                WHERE s.token = ? AND s.expires_at > NOW() AND u.status = 'active'
            ");
            $stmt->execute([$token]);
            $user = $stmt->fetch();

            if (!$user) {
                http_response_code(401);
                echo json_encode(['success' => false, 'error' => 'Session invalide ou expirée']);
                exit;
            }

            echo json_encode([
                'success' => true,
                'user' => [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'name' => $user['name'],
                    'role' => $user['role']
                ]
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Erreur serveur']);
            error_log("Erreur verify: " . $e->getMessage());
        }
        exit;
    }

    if ($action === 'logout') {
        $token = $input['token'] ?? '';

        if (!empty($token)) {
            try {
                $stmt = db()->prepare("DELETE FROM sessions WHERE token = ?");
                $stmt->execute([$token]);
            } catch (Exception $e) {
                error_log("Erreur logout: " . $e->getMessage());
            }
        }

        echo json_encode(['success' => true]);
        exit;
    }

    if ($action === 'create_user') {
        // Vérifier clé API pour créer utilisateur
        if (!verifyApiKey()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Clé API invalide']);
            exit;
        }

        $email = trim($input['email'] ?? '');
        $name = trim($input['name'] ?? '');
        $password = $input['password'] ?? '';
        $role = $input['role'] ?? 'admin';

        if (empty($email) || empty($name) || empty($password)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Données manquantes']);
            exit;
        }

        try {
            // Vérifier si existe
            $stmt = db()->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                http_response_code(409);
                echo json_encode(['success' => false, 'error' => 'Cet email existe déjà']);
                exit;
            }

            // Créer
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = db()->prepare("
                INSERT INTO users (email, name, password_hash, role) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$email, $name, $hash, $role]);

            echo json_encode([
                'success' => true,
                'id' => db()->lastInsertId()
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Erreur serveur']);
            error_log("Erreur create_user: " . $e->getMessage());
        }
        exit;
    }
}

http_response_code(400);
echo json_encode(['success' => false, 'error' => 'Action non reconnue']);
