<?php

/**
 * BASE DE DONNÉES - Système admin minimal
 * Initialisation et gestion simplifiée
 */

require_once __DIR__ . '/config.php';

class Database
{
    private static $pdo = null;

    public static function connect(): PDO
    {
        if (self::$pdo === null) {
            try {
                if (USE_SQLITE) {
                    $dsn = 'sqlite:' . SQLITE_PATH;
                    self::$pdo = new PDO($dsn, null, null, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]);
                    self::$pdo->exec('PRAGMA foreign_keys = ON');
                } else {
                    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
                    self::$pdo = new PDO($dsn, DB_USER, DB_PASS, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]);
                }

                // Initialiser les tables si nécessaire
                self::init();
            } catch (PDOException $e) {
                die("❌ Erreur de connexion DB : " . htmlspecialchars($e->getMessage()));
            }
        }
        return self::$pdo;
    }

    public static function init(): bool
    {
        try {
            $pdo = self::$pdo;

            if (USE_SQLITE) {
                // SQLite syntax
                $pdo->exec("
                    CREATE TABLE IF NOT EXISTS admins (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        nom VARCHAR(120) NOT NULL,
                        email VARCHAR(190) NOT NULL UNIQUE,
                        password_hash VARCHAR(255) NOT NULL,
                        role VARCHAR(20) NOT NULL DEFAULT 'admin',
                        actif INTEGER DEFAULT 1,
                        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                    )
                ");

                $pdo->exec("CREATE INDEX IF NOT EXISTS idx_admins_email ON admins(email)");

                $pdo->exec("
                    CREATE TABLE IF NOT EXISTS sessions (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        admin_id INTEGER NOT NULL,
                        token VARCHAR(255) NOT NULL UNIQUE,
                        ip_address VARCHAR(45),
                        user_agent VARCHAR(255),
                        expires_at DATETIME,
                        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                        FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE
                    )
                ");

                $pdo->exec("CREATE INDEX IF NOT EXISTS idx_sessions_token ON sessions(token)");
                $pdo->exec("CREATE INDEX IF NOT EXISTS idx_sessions_admin ON sessions(admin_id)");

                $pdo->exec("
                    CREATE TABLE IF NOT EXISTS logs (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        admin_id INTEGER,
                        action VARCHAR(50) NOT NULL,
                        details TEXT,
                        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                        FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL
                    )
                ");

                $pdo->exec("CREATE INDEX IF NOT EXISTS idx_logs_admin ON logs(admin_id)");
                $pdo->exec("Create INDEX IF NOT EXISTS idx_logs_action ON logs(action)");
                $pdo->exec("CREATE INDEX IF NOT EXISTS idx_logs_created ON logs(created_at)");

                // Table publicités
                $pdo->exec("
                    CREATE TABLE IF NOT EXISTS publicites (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        titre VARCHAR(255) NOT NULL,
                        description TEXT,
                        lien VARCHAR(500),
                        budget DECIMAL(10, 2) DEFAULT 0,
                        statut VARCHAR(20) DEFAULT 'inactive',
                        date_debut DATE,
                        date_fin DATE,
                        admin_id INTEGER,
                        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                        FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL
                    )
                ");

                $pdo->exec("CREATE INDEX IF NOT EXISTS idx_publicites_statut ON publicites(statut)");
                $pdo->exec("CREATE INDEX IF NOT EXISTS idx_publicites_dates ON publicites(date_debut, date_fin)");
            } else {
                // MySQL syntax
                $pdo->exec("
                    CREATE TABLE IF NOT EXISTS admins (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        nom VARCHAR(120) NOT NULL,
                        email VARCHAR(190) NOT NULL UNIQUE,
                        password_hash VARCHAR(255) NOT NULL,
                        role ENUM('super_admin', 'admin') NOT NULL DEFAULT 'admin',
                        actif TINYINT(1) DEFAULT 1,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        INDEX(email)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                ");

                $pdo->exec("
                    CREATE TABLE IF NOT EXISTS sessions (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        admin_id INT NOT NULL,
                        token VARCHAR(255) NOT NULL UNIQUE,
                        ip_address VARCHAR(45),
                        user_agent VARCHAR(255),
                        expires_at DATETIME,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE,
                        INDEX(token),
                        INDEX(admin_id)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                ");

                $pdo->exec("
                    CREATE TABLE IF NOT EXISTS logs (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        admin_id INT,
                        action VARCHAR(50) NOT NULL,
                        details TEXT,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL,
                        INDEX(admin_id),
                        INDEX(action),
                        INDEX(created_at)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                ");

                // Table publicités
                $pdo->exec("
                    CREATE TABLE IF NOT EXISTS publicites (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        titre VARCHAR(255) NOT NULL,
                        description LONGTEXT,
                        lien VARCHAR(500),
                        budget DECIMAL(10, 2) DEFAULT 0,
                        statut VARCHAR(20) DEFAULT 'inactive',
                        date_debut DATE,
                        date_fin DATE,
                        admin_id INT,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL,
                        INDEX(statut),
                        INDEX(date_debut),
                        INDEX(date_fin)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                ");
            }

            return true;
        } catch (Exception $e) {
            error_log("Erreur initialisation DB: " . $e->getMessage());
            return false;
        }
    }
}
