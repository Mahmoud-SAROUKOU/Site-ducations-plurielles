<?php

/**
 * EXEMPLE D'INTÉGRATION
 * Copiez ce code pour intégrer l'authentification dans vos pages
 */

// ============================================
// Exemple 1 : Page protégée simple
// ============================================

/*
<?php
// Inclure le système d'authentification
require_once __DIR__ . '/admin/auth.php';

// Exiger une connexion
$auth->requireLogin();

// Récupérer l'utilisateur
$user = $auth->getCurrentUser();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Page protégée</title>
</head>
<body>
    <h1>Bienvenue <?= htmlspecialchars($user['name']) ?></h1>
    <p>Email: <?= htmlspecialchars($user['email']) ?></p>
    <p>Rôle: <?= htmlspecialchars($user['role']) ?></p>
    
    <a href="/admin/logout-unified.php">Déconnexion</a>
</body>
</html>
*/


// ============================================
// Exemple 2 : Rôle spécifique
// ============================================

/*
<?php
require_once __DIR__ . '/admin/auth.php';

// Réserver à un rôle spécifique
$auth->requireRole('admin');

// Code accessible uniquement aux admins
echo "Vous êtes administrateur";
?>
*/


// ============================================
// Exemple 3 : Vérification optionnelle
// ============================================

/*
<?php
require_once __DIR__ . '/admin/auth.php';

if ($auth->isLoggedIn()) {
    $user = $auth->getCurrentUser();
    echo "Connecté en tant que: " . $user['name'];
} else {
    echo "Non connecté. <a href='/admin/login-unified.php'>Se connecter</a>";
}
?>
*/


// ============================================
// Exemple 4 : Navigation conditionnelle
// ============================================

/*
<!DOCTYPE html>
<html>
<head>
    <title>Site</title>
</head>
<body>
    <nav>
        <a href="/">Accueil</a>
        <?php
        require_once __DIR__ . '/admin/auth.php';
        
        if ($auth->isLoggedIn()) {
            $user = $auth->getCurrentUser();
            ?>
            <a href="/admin/dashboard-unified.php">Tableau de bord</a>
            <?php if ($user['role'] === 'super_admin' || $user['role'] === 'admin'): ?>
                <a href="/admin/users.php">Gestion utilisateurs</a>
            <?php endif; ?>
            <a href="/admin/logout-unified.php">Déconnexion</a>
            <?php
        } else {
            ?>
            <a href="/admin/login-unified.php">Connexion</a>
            <?php
        }
        ?>
    </nav>
    
    <!-- Contenu du site -->
</body>
</html>
*/


// ============================================
// Exemple 5 : Articles avec auteur
// ============================================

/*
<?php
require_once __DIR__ . '/admin/auth.php';
$auth->requireLogin();

$user = $auth->getCurrentUser();

// Créer un article
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    
    $stmt = db()->prepare(
        'INSERT INTO articles (title, content, author_id, created_at) 
         VALUES (?, ?, ?, NOW())'
    );
    $stmt->execute([$title, $content, $user['id']]);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Créer un article</title>
</head>
<body>
    <h1>Nouvel article</h1>
    <form method="post">
        <input type="text" name="title" required />
        <textarea name="content" required></textarea>
        <button type="submit">Créer</button>
    </form>
</body>
</html>
*/


// ============================================
// Exemple 6 : Gestion avec permissions
// ============================================

/*
<?php
require_once __DIR__ . '/admin/auth.php';
$auth->requireLogin();

$user = $auth->getCurrentUser();
$canEdit = in_array($user['role'], ['super_admin', 'admin', 'editor']);

// Récupérer un article
$articleId = $_GET['id'] ?? 0;
$stmt = db()->prepare(
    'SELECT id, title, content, author_id 
     FROM articles WHERE id = ?'
);
$stmt->execute([$articleId]);
$article = $stmt->fetch();

if (!$article) {
    http_response_code(404);
    exit('Article non trouvé');
}

// Vérifier la permission (auteur ou admin)
$isOwner = $article['author_id'] === $user['id'];
$canModify = $user['role'] === 'super_admin' || 
             $user['role'] === 'admin' || 
             ($user['role'] === 'editor' && $isOwner);

if (!$canModify && $_SERVER['REQUEST_METHOD'] === 'POST') {
    http_response_code(403);
    exit('Accès refusé');
}

// Mettre à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $canModify) {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    
    $stmt = db()->prepare(
        'UPDATE articles SET title = ?, content = ? WHERE id = ?'
    );
    $stmt->execute([$title, $content, $articleId]);
    
    echo "Article mis à jour";
}
?>

<!DOCTYPE html>
<html>
<body>
    <h1><?= htmlspecialchars($article['title']) ?></h1>
    
    <?php if ($canModify): ?>
        <form method="post">
            <input type="text" name="title" value="<?= htmlspecialchars($article['title']) ?>" />
            <textarea name="content"><?= htmlspecialchars($article['content']) ?></textarea>
            <button type="submit">Mettre à jour</button>
        </form>
    <?php else: ?>
        <p><?= htmlspecialchars($article['content']) ?></p>
    <?php endif; ?>
</body>
</html>
*/


// ============================================
// Exemple 7 : Changement de mot de passe
// ============================================

/*
<?php
require_once __DIR__ . '/admin/auth.php';
$auth->requireLogin();

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oldPassword = $_POST['old_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if ($newPassword !== $confirmPassword) {
        $error = 'Les mots de passe ne correspondent pas';
    } else {
        $result = $auth->changePassword(
            $_SESSION['user_id'],
            $oldPassword,
            $newPassword
        );
        
        if ($result['success']) {
            $success = 'Mot de passe changé avec succès';
        } else {
            $error = $result['error'];
        }
    }
}
?>

<!DOCTYPE html>
<html>
<body>
    <h1>Changer mon mot de passe</h1>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <form method="post">
        <input type="password" name="old_password" placeholder="Ancien mot de passe" required />
        <input type="password" name="new_password" placeholder="Nouveau mot de passe" required />
        <input type="password" name="confirm_password" placeholder="Confirmer" required />
        <button type="submit">Changer</button>
    </form>
</body>
</html>
*/


// ============================================
// Exemple 8 : Audit et logging
// ============================================

/*
<?php
require_once __DIR__ . '/admin/auth.php';
$auth->requireRole('super_admin');

// Récupérer le log d'audit des 7 derniers jours
$stmt = db()->prepare(
    'SELECT user_id, action, entity_type, entity_id, created_at 
     FROM audit_logs 
     WHERE created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)
     ORDER BY created_at DESC'
);
$stmt->execute();
$logs = $stmt->fetchAll();

foreach ($logs as $log) {
    echo $log['created_at'] . ' - ' . 
         $log['action'] . ' on ' . 
         $log['entity_type'] . ' #' . 
         $log['entity_id'] . '<br>';
}
?>
*/


?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <title>Exemples d'intégration</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            font-family: monospace;
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
        }

        h2 {
            color: #4ec9b0;
            margin-top: 30px;
            margin-bottom: 10px;
        }

        pre {
            background: #252526;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            border-left: 4px solid #007acc;
        }
    </style>
</head>

<body>
    <h1>📚 Exemples d'intégration - Système Admin Unifié</h1>

    <p style="margin-bottom: 30px; color: #999;">
        Voir les commentaires dans ce fichier pour des exemples complets d'utilisation.
    </p>

    <h2>1️⃣ Page protégée simple</h2>
    <p>Demande une connexion avant d'accéder à la page</p>

    <h2>2️⃣ Rôle spécifique</h2>
    <p>Réserver du contenu à un rôle particulier</p>

    <h2>3️⃣ Vérification optionnelle</h2>
    <p>Afficher du contenu différent selon la connexion</p>

    <h2>4️⃣ Navigation conditionnelle</h2>
    <p>Menu avec liens différents selon le rôle</p>

    <h2>5️⃣ Articles avec auteur</h2>
    <p>Créer des articles liés à l'utilisateur connecté</p>

    <h2>6️⃣ Gestion avec permissions</h2>
    <p>Autoriser la modification selon le rôle ou la propriété</p>

    <h2>7️⃣ Changement de mot de passe</h2>
    <p>Page pour changer le mot de passe de l'utilisateur</p>

    <h2>8️⃣ Audit et logging</h2>
    <p>Récupérer et afficher les logs d'audit</p>

    <hr style="margin-top: 40px; border: none; border-top: 1px solid #333;">

    <p style="margin-top: 20px; color: #999;">
        <strong>Source:</strong> Ouvrez ce fichier dans un éditeur de code pour voir les exemples complets
    </p>
</body>

</html>