<?php
/**
 * 🔍 DIAGNOSTIC BASE DE DONNÉES SQLITE
 * Vérification de l'état de database.sqlite
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🗄️ Diagnostic BD SQLite</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .alert-success { background: #d1fae5; color: #065f46; border: 2px solid #10b981; }
        .alert-warning { background: #fef3c7; color: #92400e; border: 2px solid #f59e0b; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 2px solid #ef4444; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background: #1e3a8a;
            color: white;
            font-weight: 600;
        }
        tr:hover { background: #f8f9fa; }
        pre {
            background: #1e293b;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 style="font-size: 32px; margin-bottom: 10px;">🗄️ Diagnostic Base de Données SQLite</h1>
        <p style="color: #666; margin-bottom: 30px;">Vérification de admin/database.sqlite</p>

        <?php
        $dbPath = __DIR__ . '/admin/database.sqlite';
        
        // Vérification existence fichier
        echo '<div class="section">';
        echo '<h2 style="font-size: 24px; margin-bottom: 15px;">📁 Fichier database.sqlite</h2>';
        
        if (!file_exists($dbPath)) {
            echo '<div class="alert alert-error">';
            echo '<strong>❌ FICHIER NON TROUVÉ</strong><br>';
            echo "Chemin: <code>$dbPath</code><br>";
            echo 'Le fichier database.sqlite n\'existe pas. Exécutez admin/install.php pour le créer.';
            echo '</div>';
            echo '</div></div></body></html>';
            exit;
        }
        
        $fileSize = filesize($dbPath);
        $filePerms = substr(sprintf('%o', fileperms($dbPath)), -4);
        $fileModified = date('Y-m-d H:i:s', filemtime($dbPath));
        
        echo '<div class="alert alert-success">';
        echo '<strong>✅ FICHIER TROUVÉ</strong><br>';
        echo "Chemin: <code>$dbPath</code><br>";
        echo "Taille: <strong>" . number_format($fileSize / 1024, 2) . " KB</strong><br>";
        echo "Permissions: <code>$filePerms</code><br>";
        echo "Modifié: <strong>$fileModified</strong>";
        echo '</div>';
        echo '</div>';
        
        // Connexion à la base
        try {
            $pdo = new PDO('sqlite:' . $dbPath);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            echo '<div class="section">';
            echo '<h2 style="font-size: 24px; margin-bottom: 15px;">🔌 Connexion</h2>';
            echo '<div class="alert alert-success">✅ Connexion PDO réussie</div>';
            echo '</div>';
            
            // Liste des tables
            echo '<div class="section">';
            echo '<h2 style="font-size: 24px; margin-bottom: 15px;">📋 Tables</h2>';
            $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            if (empty($tables)) {
                echo '<div class="alert alert-warning">⚠️ Aucune table trouvée. Base de données vide.</div>';
            } else {
                echo '<div class="alert alert-success">✅ ' . count($tables) . ' table(s) trouvée(s)</div>';
                echo '<ul>';
                foreach ($tables as $table) {
                    echo "<li><code>$table</code></li>";
                }
                echo '</ul>';
            }
            echo '</div>';
            
            // Vérification table admins
            if (in_array('admins', $tables)) {
                echo '<div class="section">';
                echo '<h2 style="font-size: 24px; margin-bottom: 15px;">👥 Table ADMINS</h2>';
                
                $stmt = $pdo->query("SELECT COUNT(*) FROM admins");
                $count = $stmt->fetchColumn();
                
                if ($count === 0) {
                    echo '<div class="alert alert-warning">';
                    echo '<strong>⚠️ TABLE VIDE</strong><br>';
                    echo 'Aucun administrateur trouvé dans la base de données SQLite.<br>';
                    echo 'Cela explique pourquoi rien ne s\'affiche dans admin.html';
                    echo '</div>';
                } else {
                    echo '<div class="alert alert-success">✅ ' . $count . ' administrateur(s) dans la base</div>';
                    
                    $stmt = $pdo->query("SELECT * FROM admins ORDER BY created_at DESC");
                    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    echo '<table>';
                    echo '<thead><tr><th>ID</th><th>Nom</th><th>Email</th><th>Rôle</th><th>Actif</th><th>Créé le</th></tr></thead>';
                    echo '<tbody>';
                    foreach ($admins as $admin) {
                        $active = isset($admin['actif']) ? ($admin['actif'] ? '✅' : '❌') : 'N/A';
                        $created = $admin['created_at'] ?? 'N/A';
                        echo '<tr>';
                        echo "<td>{$admin['id']}</td>";
                        echo "<td>{$admin['nom']}</td>";
                        echo "<td>{$admin['email']}</td>";
                        echo "<td>{$admin['role']}</td>";
                        echo "<td>$active</td>";
                        echo "<td>$created</td>";
                        echo '</tr>';
                    }
                    echo '</tbody></table>';
                }
                echo '</div>';
            }
            
            // Vérification table publicites
            if (in_array('publicites', $tables)) {
                echo '<div class="section">';
                echo '<h2 style="font-size: 24px; margin-bottom: 15px;">📢 Table PUBLICITES</h2>';
                
                $stmt = $pdo->query("SELECT COUNT(*) FROM publicites");
                $count = $stmt->fetchColumn();
                
                if ($count === 0) {
                    echo '<div class="alert alert-warning">⚠️ Table vide</div>';
                } else {
                    echo '<div class="alert alert-success">✅ ' . $count . ' publicité(s) dans la base</div>';
                    
                    $stmt = $pdo->query("SELECT * FROM publicites ORDER BY created_at DESC LIMIT 5");
                    $pubs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    echo '<table>';
                    echo '<thead><tr><th>ID</th><th>Titre</th><th>Statut</th><th>Budget</th><th>Créé le</th></tr></thead>';
                    echo '<tbody>';
                    foreach ($pubs as $pub) {
                        echo '<tr>';
                        echo "<td>{$pub['id']}</td>";
                        echo "<td>{$pub['titre']}</td>";
                        echo "<td>{$pub['statut']}</td>";
                        echo "<td>{$pub['budget']}</td>";
                        echo "<td>{$pub['created_at']}</td>";
                        echo '</tr>';
                    }
                    echo '</tbody></table>';
                }
                echo '</div>';
            }
            
            // Vérification table logs
            if (in_array('logs', $tables)) {
                echo '<div class="section">';
                echo '<h2 style="font-size: 24px; margin-bottom: 15px;">📜 Derniers logs</h2>';
                
                $stmt = $pdo->query("SELECT * FROM logs ORDER BY created_at DESC LIMIT 10");
                $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (empty($logs)) {
                    echo '<div class="alert alert-warning">⚠️ Aucun log</div>';
                } else {
                    echo '<table>';
                    echo '<thead><tr><th>Action</th><th>Admin ID</th><th>Détails</th><th>Date</th></tr></thead>';
                    echo '<tbody>';
                    foreach ($logs as $log) {
                        echo '<tr>';
                        echo "<td>{$log['action']}</td>";
                        echo "<td>{$log['admin_id']}</td>";
                        echo "<td>" . substr($log['details'] ?? '', 0, 50) . "</td>";
                        echo "<td>{$log['created_at']}</td>";
                        echo '</tr>';
                    }
                    echo '</tbody></table>';
                }
                echo '</div>';
            }
            
            // Structure de la table admins
            echo '<div class="section">';
            echo '<h2 style="font-size: 24px; margin-bottom: 15px;">🔧 Structure table ADMINS</h2>';
            
            if (in_array('admins', $tables)) {
                $stmt = $pdo->query("PRAGMA table_info(admins)");
                $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo '<table>';
                echo '<thead><tr><th>Colonne</th><th>Type</th><th>Nullable</th><th>Défaut</th></tr></thead>';
                echo '<tbody>';
                foreach ($columns as $col) {
                    echo '<tr>';
                    echo "<td><code>{$col['name']}</code></td>";
                    echo "<td>{$col['type']}</td>";
                    echo "<td>" . ($col['notnull'] ? 'Non' : 'Oui') . "</td>";
                    echo "<td>" . ($col['dflt_value'] ?? 'NULL') . "</td>";
                    echo '</tr>';
                }
                echo '</tbody></table>';
            } else {
                echo '<div class="alert alert-error">❌ Table admins non trouvée</div>';
            }
            echo '</div>';
            
            // Diagnostic final
            echo '<div class="section">';
            echo '<h2 style="font-size: 24px; margin-bottom: 15px;">🎯 Diagnostic</h2>';
            
            if (!in_array('admins', $tables)) {
                echo '<div class="alert alert-error">';
                echo '<strong>❌ PROBLÈME CRITIQUE</strong><br>';
                echo 'La table "admins" n\'existe pas. Exécutez <code>admin/install.php</code> pour initialiser la base.';
                echo '</div>';
            } else {
                $stmt = $pdo->query("SELECT COUNT(*) FROM admins");
                $adminCount = $stmt->fetchColumn();
                
                if ($adminCount === 0) {
                    echo '<div class="alert alert-error">';
                    echo '<strong>❌ BASE DE DONNÉES VIDE</strong><br>';
                    echo 'La table admins existe mais est vide.<br>';
                    echo '<strong>Solution:</strong> Les données sont probablement dans localStorage mais pas synchronisées avec la BD SQLite.<br>';
                    echo 'Ouvrez <code>diagnostic-donnees.html</code> pour vérifier localStorage et exporter les données.';
                    echo '</div>';
                } else {
                    echo '<div class="alert alert-success">';
                    echo '<strong>✅ BASE DE DONNÉES OPÉRATIONNELLE</strong><br>';
                    echo "$adminCount administrateur(s) dans la base SQLite.<br>";
                    echo 'Si admin.html n\'affiche rien, c\'est un problème de synchronisation localStorage ↔ BD.';
                    echo '</div>';
                }
            }
            echo '</div>';
            
        } catch (PDOException $e) {
            echo '<div class="section">';
            echo '<div class="alert alert-error">';
            echo '<strong>❌ ERREUR DE CONNEXION</strong><br>';
            echo 'Message: <code>' . htmlspecialchars($e->getMessage()) . '</code>';
            echo '</div>';
            echo '</div>';
        }
        ?>

        <div class="section">
            <h2 style="font-size: 24px; margin-bottom: 15px;">🔗 Actions recommandées</h2>
            <ul style="line-height: 2;">
                <li>✅ <a href="diagnostic-donnees.html" style="color: #0073aa; text-decoration: none; font-weight: 600;">Vérifier localStorage</a> (données côté client)</li>
                <li>✅ <a href="admin.html" style="color: #0073aa; text-decoration: none; font-weight: 600;">Ouvrir admin.html</a> (interface admin)</li>
                <li>✅ <a href="admin/install.php" style="color: #0073aa; text-decoration: none; font-weight: 600;">Réinstaller la base</a> (si nécessaire)</li>
                <li>✅ <a href="admin/test-auth.php" style="color: #0073aa; text-decoration: none; font-weight: 600;">Tester authentification</a></li>
            </ul>
        </div>
    </div>
</body>
</html>
