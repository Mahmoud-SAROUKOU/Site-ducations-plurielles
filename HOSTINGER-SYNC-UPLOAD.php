<?php

/**
 * HOSTINGER - FICHIER DE SYNCHRONISATION
 * Uploadez ce fichier sur votre hébergement (ex: /admin/api/sync.php)
 * Puis configurez l'URL et la clé dans admin.html > Paramètres > Synchronisation
 */

// ====== CONFIGURATION ======
define('DB_HOST', 'localhost');
define('DB_NAME', 'educations_plurielles');
define('DB_USER', 'root');
define('DB_PASS', '');
define('ADMIN_SYNC_KEY', 'change_me');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Admin-Sync-Key');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
    exit;
}

$providedKey = $_SERVER['HTTP_X_ADMIN_SYNC_KEY'] ?? ($_POST['sync_key'] ?? '');
if (!ADMIN_SYNC_KEY || $providedKey !== ADMIN_SYNC_KEY) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Clé de synchronisation invalide']);
    exit;
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    return $pdo;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    $input = $_POST;
}

$type = trim($input['type'] ?? '');
$operation = trim($input['operation'] ?? 'create');
$data = $input['data'] ?? null;

if ($type === '' || !is_array($data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Données invalides']);
    exit;
}

function tableExists(string $table): bool
{
    try {
        $stmt = db()->prepare("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? LIMIT 1");
        $stmt->execute([DB_NAME, $table]);
        return (bool)$stmt->fetchColumn();
    } catch (Exception $e) {
        return false;
    }
}

function slugify(string $text): string
{
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = trim($text, '-');
    $text = strtolower($text);
    $text = preg_replace('~[^-a-z0-9]+~', '', $text);
    return $text ?: 'article';
}

function getOrCreateUser(string $name, string $email): int
{
    $name = trim($name) !== '' ? trim($name) : 'Admin Local';
    $email = trim($email) !== '' ? trim(strtolower($email)) : 'local-sync@educations-plurielles.local';

    $stmt = db()->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $id = $stmt->fetchColumn();
    if ($id) {
        return (int)$id;
    }

    $randomPass = bin2hex(random_bytes(8));
    $hash = password_hash($randomPass, PASSWORD_BCRYPT);
    $stmt = db()->prepare('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)');
    $stmt->execute([$name, $email, $hash]);
    return (int)db()->lastInsertId();
}

try {
    if ($type === 'article') {
        if (!tableExists('articles') || !tableExists('users')) {
            throw new Exception('Tables articles/users introuvables');
        }

        $articleId = (int)($data['id'] ?? 0);
        $providedSlug = trim($data['slug'] ?? '');
        $title = trim($data['title'] ?? '');
        $content = trim($data['content'] ?? '');
        $category = trim($data['category'] ?? 'parentalite');
        $author = trim($data['author'] ?? 'Admin Local');
        $authorEmail = trim($data['author_email'] ?? '');
        $excerpt = trim($data['excerpt'] ?? '');
        $imageUrl = trim($data['image_url'] ?? '');
        $tags = trim($data['tags'] ?? '');
        $readTime = trim($data['read_time'] ?? '');
        $status = trim($data['status'] ?? 'published');

        if ($operation === 'delete') {
            if ($articleId > 0) {
                $stmt = db()->prepare('DELETE FROM articles WHERE id = ?');
                $stmt->execute([$articleId]);
                echo json_encode(['success' => true, 'deleted' => $stmt->rowCount()]);
                exit;
            }
            $slug = $providedSlug !== '' ? $providedSlug : ($title !== '' ? slugify($title) : '');
            if ($slug === '') {
                throw new Exception('ID ou slug requis pour suppression');
            }
            $stmt = db()->prepare('DELETE FROM articles WHERE slug = ?');
            $stmt->execute([$slug]);
            echo json_encode(['success' => true, 'deleted' => $stmt->rowCount()]);
            exit;
        }

        if ($title === '' || $content === '') {
            throw new Exception('Titre et contenu requis');
        }

        if ($excerpt === '') {
            $excerpt = mb_substr(strip_tags($content), 0, 180);
        }

        $authorId = getOrCreateUser($author, $authorEmail);

        if ($operation === 'update') {
            if ($articleId > 0) {
                $stmt = db()->prepare(
                    "UPDATE articles SET title = ?, excerpt = ?, content = ?, category = ?, image_url = ?, tags = ?, read_time = ?, status = ?, author_id = ? WHERE id = ?"
                );
                $stmt->execute([
                    $title,
                    $excerpt,
                    $content,
                    $category,
                    $imageUrl,
                    $tags,
                    $readTime,
                    $status,
                    $authorId,
                    $articleId
                ]);
                echo json_encode(['success' => true, 'updated' => $stmt->rowCount()]);
                exit;
            }

            $slug = $providedSlug !== '' ? $providedSlug : slugify($title);
            $stmt = db()->prepare(
                "UPDATE articles SET title = ?, excerpt = ?, content = ?, category = ?, image_url = ?, tags = ?, read_time = ?, status = ?, author_id = ? WHERE slug = ?"
            );
            $stmt->execute([
                $title,
                $excerpt,
                $content,
                $category,
                $imageUrl,
                $tags,
                $readTime,
                $status,
                $authorId,
                $slug
            ]);
            echo json_encode(['success' => true, 'updated' => $stmt->rowCount()]);
            exit;
        }

        $slugBase = slugify($title);
        $slug = $slugBase;
        $stmt = db()->prepare('SELECT id FROM articles WHERE slug = ?');
        $stmt->execute([$slug]);
        if ($stmt->fetch()) {
            $slug = $slugBase . '-' . substr(md5(uniqid('', true)), 0, 6);
        }

        $stmt = db()->prepare(
            "INSERT INTO articles (title, slug, excerpt, content, category, image_url, tags, read_time, status, published_at, created_at, author_id)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), ?)"
        );
        $stmt->execute([
            $title,
            $slug,
            $excerpt,
            $content,
            $category,
            $imageUrl,
            $tags,
            $readTime,
            $status,
            $authorId
        ]);
        echo json_encode(['success' => true, 'id' => (int)db()->lastInsertId()]);
        exit;
    }

    if ($type === 'category') {
        if (!tableExists('categories')) {
            echo json_encode(['success' => true, 'skipped' => true, 'message' => 'Table categories introuvable']);
            exit;
        }

        $categoryId = (int)($data['id'] ?? 0);
        $name = trim($data['name'] ?? '');
        $slug = trim($data['slug'] ?? ($name !== '' ? slugify($name) : ''));

        if ($operation === 'delete') {
            if ($categoryId > 0) {
                $stmt = db()->prepare('DELETE FROM categories WHERE id = ?');
                $stmt->execute([$categoryId]);
                echo json_encode(['success' => true, 'deleted' => $stmt->rowCount()]);
                exit;
            }
            if ($slug === '') {
                throw new Exception('ID ou slug requis pour suppression');
            }
            $stmt = db()->prepare('DELETE FROM categories WHERE slug = ?');
            $stmt->execute([$slug]);
            echo json_encode(['success' => true, 'deleted' => $stmt->rowCount()]);
            exit;
        }

        if ($name === '') {
            throw new Exception('Nom requis');
        }

        if ($operation === 'update') {
            if ($categoryId > 0) {
                $stmt = db()->prepare('UPDATE categories SET name = ?, slug = ? WHERE id = ?');
                $stmt->execute([$name, $slug, $categoryId]);
                echo json_encode(['success' => true, 'updated' => $stmt->rowCount()]);
                exit;
            }
            $stmt = db()->prepare('UPDATE categories SET name = ? WHERE slug = ?');
            $stmt->execute([$name, $slug]);
            echo json_encode(['success' => true, 'updated' => $stmt->rowCount()]);
            exit;
        }

        $stmt = db()->prepare('SELECT id FROM categories WHERE slug = ?');
        $stmt->execute([$slug]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => true, 'message' => 'Catégorie déjà existante']);
            exit;
        }

        $stmt = db()->prepare('INSERT INTO categories (name, slug, created_at) VALUES (?, ?, NOW())');
        $stmt->execute([$name, $slug]);
        echo json_encode(['success' => true, 'id' => (int)db()->lastInsertId()]);
        exit;
    }

    if ($type === 'ad') {
        if (!tableExists('ads')) {
            throw new Exception('Table ads introuvable');
        }

        $adId = (int)($data['id'] ?? 0);
        $name = trim($data['name'] ?? '');
        $message = trim($data['message'] ?? '');
        $icon = trim($data['icon'] ?? '📢');
        $position = trim($data['position'] ?? 'sidebar');
        $imageUrl = trim($data['image_url'] ?? '');
        $targetUrl = trim($data['target_url'] ?? '');
        $status = trim($data['status'] ?? 'active');
        $displayOrder = (int)($data['display_order'] ?? 0);

        if ($operation === 'delete') {
            if ($adId > 0) {
                $stmt = db()->prepare('DELETE FROM ads WHERE id = ?');
                $stmt->execute([$adId]);
                echo json_encode(['success' => true, 'deleted' => $stmt->rowCount()]);
                exit;
            }
            if ($name === '') {
                throw new Exception('ID ou nom requis pour suppression');
            }
            $stmt = db()->prepare('DELETE FROM ads WHERE name = ?');
            $stmt->execute([$name]);
            echo json_encode(['success' => true, 'deleted' => $stmt->rowCount()]);
            exit;
        }

        if ($name === '') {
            throw new Exception('Nom requis');
        }

        if ($operation === 'update') {
            if ($adId > 0) {
                $stmt = db()->prepare(
                    "UPDATE ads SET name = ?, message = ?, icon = ?, position = ?, image_url = ?, target_url = ?, status = ?, display_order = ? WHERE id = ?"
                );
                $stmt->execute([
                    $name,
                    $message ?: $name,
                    $icon,
                    $position,
                    $imageUrl,
                    $targetUrl,
                    $status,
                    $displayOrder,
                    $adId
                ]);
                echo json_encode(['success' => true, 'updated' => $stmt->rowCount()]);
                exit;
            }

            $stmt = db()->prepare(
                "UPDATE ads SET message = ?, icon = ?, position = ?, image_url = ?, target_url = ?, status = ?, display_order = ? WHERE name = ?"
            );
            $stmt->execute([
                $message ?: $name,
                $icon,
                $position,
                $imageUrl,
                $targetUrl,
                $status,
                $displayOrder,
                $name
            ]);
            echo json_encode(['success' => true, 'updated' => $stmt->rowCount()]);
            exit;
        }

        $stmt = db()->prepare(
            "INSERT INTO ads (name, message, icon, position, image_url, target_url, status, display_order, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())"
        );
        $stmt->execute([
            $name,
            $message ?: $name,
            $icon,
            $position,
            $imageUrl,
            $targetUrl,
            $status,
            $displayOrder
        ]);

        echo json_encode(['success' => true, 'id' => (int)db()->lastInsertId()]);
        exit;
    }

    if ($type === 'admin') {
        $name = trim($data['nom'] ?? $data['name'] ?? '');
        $email = trim(strtolower($data['email'] ?? ''));
        $previousEmail = trim(strtolower($data['previous_email'] ?? ''));
        $password = $data['password'] ?? '';
        $role = trim($data['role'] ?? 'admin');

        if ($operation === 'delete') {
            $lookupEmail = $previousEmail !== '' ? $previousEmail : $email;
            if ($lookupEmail === '') {
                throw new Exception('Email requis pour suppression');
            }
            $deleted = [];
            if (tableExists('admins')) {
                $stmt = db()->prepare('DELETE FROM admins WHERE email = ?');
                $stmt->execute([$lookupEmail]);
                $deleted[] = 'admins';
            }
            if (tableExists('users')) {
                $stmt = db()->prepare('DELETE FROM users WHERE email = ?');
                $stmt->execute([$lookupEmail]);
                $deleted[] = 'users';
            }
            echo json_encode(['success' => true, 'deleted_from' => $deleted]);
            exit;
        }

        if ($operation === 'update') {
            $lookupEmail = $previousEmail !== '' ? $previousEmail : $email;
            if ($lookupEmail === '') {
                throw new Exception('Email requis pour mise à jour');
            }

            $updated = [];
            if (tableExists('admins')) {
                $fields = [];
                $params = [];
                if ($name !== '') {
                    $fields[] = 'nom = ?';
                    $params[] = $name;
                }
                if ($email !== '') {
                    $fields[] = 'email = ?';
                    $params[] = $email;
                }
                if ($role !== '') {
                    $fields[] = 'role = ?';
                    $params[] = ($role === 'super_admin' ? 'super_admin' : 'admin');
                }
                if ($password !== '') {
                    $fields[] = 'password_hash = ?';
                    $params[] = password_hash($password, PASSWORD_BCRYPT);
                }
                if (!empty($fields)) {
                    $params[] = $lookupEmail;
                    $stmt = db()->prepare('UPDATE admins SET ' . implode(', ', $fields) . ' WHERE email = ?');
                    $stmt->execute($params);
                    $updated[] = 'admins';
                }
            }

            if (tableExists('users')) {
                $fields = [];
                $params = [];
                if ($name !== '') {
                    $fields[] = 'name = ?';
                    $params[] = $name;
                }
                if ($email !== '') {
                    $fields[] = 'email = ?';
                    $params[] = $email;
                }
                if ($password !== '') {
                    $fields[] = 'password_hash = ?';
                    $params[] = password_hash($password, PASSWORD_BCRYPT);
                }
                if (!empty($fields)) {
                    $params[] = $lookupEmail;
                    $stmt = db()->prepare('UPDATE users SET ' . implode(', ', $fields) . ' WHERE email = ?');
                    $stmt->execute($params);
                    $updated[] = 'users';
                }
            }

            echo json_encode(['success' => true, 'updated_in' => $updated]);
            exit;
        }

        if ($name === '' || $email === '' || $password === '') {
            throw new Exception('Nom, email et mot de passe requis');
        }

        $created = [];
        $adminId = null;
        $userId = null;

        if (tableExists('admins')) {
            $stmt = db()->prepare('SELECT id FROM admins WHERE email = ?');
            $stmt->execute([$email]);
            if (!$stmt->fetch()) {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = db()->prepare('INSERT INTO admins (nom, email, password_hash, role, created_at) VALUES (?, ?, ?, ?, NOW())');
                $stmt->execute([$name, $email, $hash, $role === 'super_admin' ? 'super_admin' : 'admin']);
                $created[] = 'admins';
                $adminId = (int)db()->lastInsertId();
            }
        }

        if (tableExists('users')) {
            $stmt = db()->prepare('SELECT id FROM users WHERE email = ?');
            $stmt->execute([$email]);
            if (!$stmt->fetch()) {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = db()->prepare('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)');
                $stmt->execute([$name, $email, $hash]);
                $created[] = 'users';
                $userId = (int)db()->lastInsertId();
            }
        }

        if (empty($created)) {
            echo json_encode(['success' => true, 'message' => 'Admin déjà présent']);
            exit;
        }

        echo json_encode([
            'success' => true,
            'created_in' => $created,
            'id_admins' => $adminId,
            'id_users' => $userId
        ]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Type non supporté']);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
