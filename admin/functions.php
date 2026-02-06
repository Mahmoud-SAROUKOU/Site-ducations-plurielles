<?php

/**
 * FONCTIONS UTILITAIRES API
 * Fournit db(), is_logged_in(), current_user(), require_login()
 */

require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

function current_user(): ?array
{
    if (!is_logged_in()) {
        return null;
    }

    $userId = (int)($_SESSION['user_id'] ?? 0);
    if ($userId <= 0) {
        return null;
    }

    try {
        $stmt = db()->prepare('SELECT id, name, email, role, created_at FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        return $user ?: null;
    } catch (Exception $e) {
        return $_SESSION['user'] ?? null;
    }
}

function require_login(): void
{
    if (!is_logged_in()) {
        http_response_code(401);
        echo json_encode(['error' => 'Non authentifié']);
        exit;
    }
}
