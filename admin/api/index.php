<?php
require_once __DIR__ . '/../functions.php';

header('Content-Type: application/json; charset=utf-8');

$action = trim($_GET['action'] ?? '');
if (!$action) {
    http_response_code(400);
    echo json_encode(['error' => 'action requise']);
    exit;
}

try {
    switch ($action) {
        // Auth endpoints
        case 'check':
            echo json_encode([
                'authenticated' => is_logged_in(),
                'user' => current_user(),
                'needs_install' => db()->query('SELECT COUNT(*) as c FROM users')->fetch()['c'] == 0
            ]);
            break;

        // Article management (admin)
        case 'articles_count':
            $count = db()->query('SELECT COUNT(*) as c FROM articles')->fetch()['c'];
            echo json_encode(['count' => (int)$count]);
            break;

        case 'articles_list':
            require_login();
            $stmt = db()->query('SELECT a.*, u.name as author_name FROM articles a JOIN users u ON u.id = a.author_id ORDER BY a.created_at DESC');
            $articles = [];
            foreach ($stmt->fetchAll() as $row) {
                $articles[] = [
                    'id' => (int)$row['id'],
                    'title' => $row['title'],
                    'category' => $row['category'] ?? 'parentalite',
                    'status' => $row['status'],
                    'author_name' => $row['author_name']
                ];
            }
            echo json_encode(['articles' => $articles]);
            break;

        case 'articles_detail':
            require_login();
            $id = (int)($_GET['id'] ?? 0);
            $stmt = db()->prepare('SELECT * FROM articles WHERE id = ?');
            $stmt->execute([$id]);
            $article = $stmt->fetch();
            if (!$article) {
                http_response_code(404);
                echo json_encode(['error' => 'article introuvable']);
                exit;
            }
            echo json_encode(['article' => $article]);
            break;

        // Ad management (admin)
        case 'ads_count':
            $count = db()->query('SELECT COUNT(*) as c FROM ads')->fetch()['c'];
            echo json_encode(['count' => (int)$count]);
            break;

        case 'ads_list':
            require_login();
            $stmt = db()->query('SELECT * FROM ads ORDER BY display_order ASC, created_at DESC');
            $ads = [];
            foreach ($stmt->fetchAll() as $row) {
                $ads[] = [
                    'id' => (int)$row['id'],
                    'name' => $row['name'],
                    'position' => $row['position'],
                    'status' => $row['status'],
                    'message' => $row['message'],
                    'icon' => $row['icon'],
                    'image_url' => $row['image_url'],
                    'target_url' => $row['target_url'],
                    'display_order' => (int)($row['display_order'] ?? 0)
                ];
            }
            echo json_encode(['ads' => $ads]);
            break;

        case 'ads_detail':
            require_login();
            $id = (int)($_GET['id'] ?? 0);
            $stmt = db()->prepare('SELECT * FROM ads WHERE id = ?');
            $stmt->execute([$id]);
            $ad = $stmt->fetch();
            if (!$ad) {
                http_response_code(404);
                echo json_encode(['error' => 'pub introuvable']);
                exit;
            }
            echo json_encode(['ad' => $ad]);
            break;

        // Admin management
        case 'admins_count':
            $count = db()->query('SELECT COUNT(*) as c FROM users')->fetch()['c'];
            echo json_encode(['count' => (int)$count]);
            break;

        case 'admins_list':
            require_login();
            $stmt = db()->query('SELECT id, name, email, created_at FROM users ORDER BY created_at DESC');
            $admins = $stmt->fetchAll();
            echo json_encode(['admins' => $admins]);
            break;

        case 'admin_create':
            require_login();
            $data = json_decode(file_get_contents('php://input'), true);
            $name = trim($data['name'] ?? '');
            $email = trim($data['email'] ?? '');
            $password = $data['password'] ?? '';

            if ($name === '' || $email === '' || $password === '') {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Champs requis']);
                exit;
            }

            if (strlen($password) < 6) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Mot de passe trop court']);
                exit;
            }

            $stmt = db()->prepare('SELECT id FROM users WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Email déjà existant']);
                exit;
            }

            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = db()->prepare('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)');
            $stmt->execute([$name, $email, $hash]);
            echo json_encode(['success' => true]);
            break;

        case 'admin_delete':
            require_login();
            $id = (int)($_GET['id'] ?? 0);
            if ($id === (current_user()['id'] ?? null)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Impossible de vous supprimer']);
                exit;
            }
            db()->prepare('DELETE FROM users WHERE id = ?')->execute([$id]);
            echo json_encode(['success' => true]);
            break;

        // Public APIs
        case 'articles':
            $stmt = db()->query("SELECT a.*, u.name as author_name FROM articles a JOIN users u ON u.id = a.author_id WHERE a.status = 'published' AND (a.published_at IS NULL OR a.published_at <= NOW()) ORDER BY COALESCE(a.published_at, a.created_at) DESC");
            $articles = [];
            foreach ($stmt->fetchAll() as $row) {
                $tags = [];
                if (!empty($row['tags'])) {
                    $tags = array_values(array_filter(array_map('trim', explode(',', $row['tags']))));
                }
                $articles[] = [
                    'id' => (int)$row['id'],
                    'title' => $row['title'],
                    'slug' => $row['slug'],
                    'excerpt' => $row['excerpt'] ?? '',
                    'category' => $row['category'] ?? 'parentalite',
                    'image' => $row['image_url'] ?? '',
                    'tags' => $tags,
                    'readTime' => $row['read_time'] ?? '',
                    'date' => !empty($row['published_at']) ? date('d F Y', strtotime($row['published_at'])) : date('d F Y', strtotime($row['created_at'])),
                    'author' => $row['author_name'] ?? ''
                ];
            }
            echo json_encode(['articles' => $articles], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            break;

        case 'ads':
            $stmt = db()->query("SELECT * FROM ads WHERE status = 'active' ORDER BY display_order ASC, created_at DESC");
            $ads = [];
            foreach ($stmt->fetchAll() as $row) {
                $ads[] = [
                    'id' => (int)$row['id'],
                    'name' => $row['name'],
                    'message' => $row['message'] ?: $row['name'],
                    'icon' => $row['icon'] ?: '📢',
                    'order' => (int)($row['display_order'] ?? 0),
                    'position' => $row['position'],
                    'image' => $row['image_url'],
                    'target' => $row['target_url']
                ];
            }
            echo json_encode(['ads' => $ads], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            break;

        case 'article':
            $slug = trim($_GET['slug'] ?? '');
            if (!$slug) {
                throw new Exception('slug requis');
            }
            $stmt = db()->prepare("SELECT a.*, u.name as author_name FROM articles a JOIN users u ON u.id = a.author_id WHERE a.slug = ? AND a.status = 'published' AND (a.published_at IS NULL OR a.published_at <= NOW()) LIMIT 1");
            $stmt->execute([$slug]);
            $row = $stmt->fetch();
            if (!$row) {
                http_response_code(404);
                echo json_encode(['error' => 'article introuvable']);
                exit;
            }
            $tags = [];
            if (!empty($row['tags'])) {
                $tags = array_values(array_filter(array_map('trim', explode(',', $row['tags']))));
            }
            echo json_encode([
                'id' => (int)$row['id'],
                'title' => $row['title'],
                'slug' => $row['slug'],
                'excerpt' => $row['excerpt'] ?? '',
                'content' => $row['content'],
                'category' => $row['category'] ?? 'parentalite',
                'image' => $row['image_url'] ?? '',
                'tags' => $tags,
                'readTime' => $row['read_time'] ?? '',
                'date' => !empty($row['published_at']) ? date('d F Y', strtotime($row['published_at'])) : date('d F Y', strtotime($row['created_at'])),
                'author' => $row['author_name'] ?? ''
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            break;

        default:
            http_response_code(400);
            echo json_encode(['error' => 'action inconnue']);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
