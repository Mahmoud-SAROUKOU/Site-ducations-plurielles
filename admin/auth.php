<?php

/**
 * AUTHENTIFICATION - Système simplifié
 * Gestion connexion/déconnexion et sessions
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/mailer.php';

class Auth
{
    const SESSION_TIMEOUT = 3600; // 1h
    const REMEMBER_TIME = 30 * 86400; // 30 jours
    const MIN_PASSWORD = 6;
    const MAX_ATTEMPTS = 5;
    const LOCKOUT_TIME = 900; // 15min

    private $pdo;
    private $admin = null;
    private $skipValidation = false;

    public function __construct($skipValidation = false)
    {
        $this->pdo = Database::connect();
        $this->skipValidation = $skipValidation;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!$skipValidation) {
            $this->validateSession();
        }
    }

    /**
     * Connexion sans mot de passe (par email seul)
     */
    public function loginWithoutPassword(string $email): array
    {
        $email = trim(strtolower($email));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'msg' => 'Email invalide'];
        }

        try {
            $stmt = $this->pdo->prepare("SELECT * FROM admins WHERE email = ? AND actif = 1");
            $stmt->execute([$email]);
            $admin = $stmt->fetch();

            if (!$admin) {
                $this->logAction(null, 'login_failed_no_pwd', "Email: $email");
                return ['success' => false, 'msg' => 'Email non reconnu'];
            }

            // Créer session
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', time() + self::SESSION_TIMEOUT);

            $stmt = $this->pdo->prepare(
                "INSERT INTO sessions (admin_id, token, ip_address, user_agent, expires_at) 
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $admin['id'],
                $token,
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? '',
                $expiresAt
            ]);

            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['token'] = $token;
            $_SESSION['timeout'] = time() + self::SESSION_TIMEOUT;

            // IMPORTANT: Stocker l'admin dans $this->admin
            $this->admin = $admin;

            $this->logAction($admin['id'], 'login_success_no_pwd', "Email: $email");

            return ['success' => true, 'msg' => 'Connexion réussie'];
        } catch (Exception $e) {
            error_log("Login without password error: " . $e->getMessage());
            return ['success' => false, 'msg' => 'Erreur serveur'];
        }
    }

    /**
     * Connexion avec mot de passe
     */
    public function login(string $email, string $password): array
    {
        $email = trim(strtolower($email));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'msg' => 'Email invalide'];
        }

        try {
            $stmt = $this->pdo->prepare("SELECT * FROM admins WHERE email = ? AND actif = 1");
            $stmt->execute([$email]);
            $admin = $stmt->fetch();

            if (!$admin || !password_verify($password, $admin['password_hash'])) {
                $this->logAction(null, 'login_failed', "Email: $email");
                return ['success' => false, 'msg' => 'Email ou mot de passe incorrect'];
            }

            // Créer session
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', time() + self::SESSION_TIMEOUT);

            $stmt = $this->pdo->prepare(
                "INSERT INTO sessions (admin_id, token, ip_address, user_agent, expires_at) 
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $admin['id'],
                $token,
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? '',
                $expiresAt
            ]);

            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['token'] = $token;
            $_SESSION['timeout'] = time() + self::SESSION_TIMEOUT;

            // IMPORTANT: Stocker l'admin dans $this->admin
            $this->admin = $admin;

            $this->logAction($admin['id'], 'login_success', "Email: $email");

            return ['success' => true, 'msg' => 'Connexion réussie'];
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return ['success' => false, 'msg' => 'Erreur serveur'];
        }
    }

    /**
     * Déconnexion
     */
    public function logout(): void
    {
        if ($this->isConnected()) {
            $this->logAction($_SESSION['admin_id'] ?? null, 'logout');

            if (isset($_SESSION['token'])) {
                $stmt = $this->pdo->prepare("DELETE FROM sessions WHERE token = ?");
                $stmt->execute([$_SESSION['token']]);
            }
        }

        session_destroy();
        // Ne pas rediriger automatiquement, laisser l'API gérer
    }

    /**
     * Créer un nouvel admin
     */
    public function createAdmin(string $nom, string $email, string $password, string $role = 'admin'): array
    {
        $email = trim(strtolower($email));

        // Validation
        if (strlen($nom) < 2 || strlen($nom) > 120) {
            return ['success' => false, 'msg' => 'Nom invalide (2-120 caractères)'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'msg' => 'Email invalide'];
        }

        if (strlen($password) < self::MIN_PASSWORD) {
            return ['success' => false, 'msg' => 'Mot de passe trop court (min ' . self::MIN_PASSWORD . ' caractères)'];
        }

        if (!in_array($role, ['super_admin', 'admin'])) {
            return ['success' => false, 'msg' => 'Rôle invalide'];
        }

        try {
            // Vérifier si existe
            $stmt = $this->pdo->prepare("SELECT id FROM admins WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                return ['success' => false, 'msg' => 'Cet email existe déjà'];
            }

            // Créer
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $this->pdo->prepare(
                "INSERT INTO admins (nom, email, password_hash, role) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$nom, $email, $hash, $role]);

            $id = $this->pdo->lastInsertId();
            $this->logAction(null, 'admin_created', "Email: $email, Role: $role");

            // Envoyer l'email de bienvenue
            try {
                Mailer::sendWelcomeEmail($nom, $email, $password);
            } catch (Exception $e) {
                error_log("Erreur envoi email: " . $e->getMessage());
            }

            return ['success' => true, 'msg' => 'Admin créé avec succès', 'id' => $id];
        } catch (Exception $e) {
            error_log("Create admin error: " . $e->getMessage());
            return ['success' => false, 'msg' => 'Erreur création admin'];
        }
    }

    /**
     * Valider la session active
     */
    private function validateSession(): void
    {
        if (!isset($_SESSION['admin_id'], $_SESSION['token'])) {
            return;
        }

        try {
            $expiresAt = date('Y-m-d H:i:s', time() + self::SESSION_TIMEOUT);
            $stmt = $this->pdo->prepare(
                "SELECT a.* FROM admins a 
                 JOIN sessions s ON a.id = s.admin_id 
                 WHERE a.id = ? AND s.token = ? AND a.actif = 1 AND s.expires_at > ?"
            );
            $stmt->execute([$_SESSION['admin_id'], $_SESSION['token'], date('Y-m-d H:i:s')]);
            $admin = $stmt->fetch();

            if (!$admin) {
                session_destroy();
                return;
            }

            $this->admin = $admin;

            // Renouveler l'expiration
            $_SESSION['timeout'] = time() + self::SESSION_TIMEOUT;
        } catch (Exception $e) {
            session_destroy();
        }
    }

    /**
     * Vérifie si connecté
     */
    public function isConnected(): bool
    {
        return $this->admin !== null;
    }

    /**
     * Retourne l'admin connecté
     */
    public function getAdmin(): ?array
    {
        return $this->admin;
    }

    /**
     * Require connexion ou redirige
     */
    public function require(): void
    {
        if (!$this->isConnected()) {
            header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
            exit;
        }
    }

    /**
     * Log une action
     */
    private function logAction(?int $adminId, string $action, string $details = ''): void
    {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO logs (admin_id, action, details) VALUES (?, ?, ?)"
            );
            $stmt->execute([$adminId, $action, $details]);
        } catch (Exception $e) {
            error_log("Log error: " . $e->getMessage());
        }
    }
}
