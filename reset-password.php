<?php

/**
 * Réinitialisation du mot de passe pour Mahmoud SAROUKOU
 */

require_once 'admin/db.php';

$email = 'saroukouy@gmail.com';
$nouveauMotDePasse = 'Educations@2026'; // Nouveau mot de passe temporaire

$pdo = Database::connect();

// Vérifier si le compte existe
$stmt = $pdo->prepare("SELECT id, nom, role, actif FROM admins WHERE email = ?");
$stmt->execute([$email]);
$admin = $stmt->fetch();

if (!$admin) {
    echo "❌ Compte introuvable pour $email\n";
    exit;
}

// Mettre à jour le mot de passe et activer le compte
$passwordHash = password_hash($nouveauMotDePasse, PASSWORD_BCRYPT);
$stmt = $pdo->prepare("UPDATE admins SET password_hash = ?, actif = 1 WHERE email = ?");
$stmt->execute([$passwordHash, $email]);

echo "✅ Compte réinitialisé avec succès !\n";
echo str_repeat('=', 70) . "\n";
echo "📧 VOS NOUVEAUX IDENTIFIANTS\n";
echo str_repeat('=', 70) . "\n";
echo "Nom         : " . $admin['nom'] . "\n";
echo "Email       : $email\n";
echo "Mot de passe: $nouveauMotDePasse\n";
echo "Rôle        : " . $admin['role'] . "\n";
echo "Statut      : ✓ Actif\n";
echo str_repeat('=', 70) . "\n";
echo "\n🔗 URL de connexion : http://localhost:8000/admin.html\n\n";
echo "⚠️  IMPORTANT : Changez ce mot de passe après votre première connexion\n";
echo "    dans Paramètres → Changer le mot de passe\n";
