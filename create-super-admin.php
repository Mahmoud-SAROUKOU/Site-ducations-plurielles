<?php

/**
 * Création de l'administrateur principal
 */

require_once 'admin/db.php';

$pdo = Database::connect();

// Données de l'administrateur principal
$nom = 'Mahmoud SAROUKOU';
$email = 'saroukouy@gmail.com';
$password = 'Admin@2026'; // À changer lors de la première connexion
$role = 'super_admin';

// Vérifier si l'admin existe déjà
$stmt = $pdo->prepare("SELECT id, actif FROM admins WHERE email = ?");
$stmt->execute([$email]);
$existing = $stmt->fetch();

if ($existing) {
    // Activer l'admin s'il existe déjà
    $stmt = $pdo->prepare("UPDATE admins SET actif = 1 WHERE email = ?");
    $stmt->execute([$email]);
    echo "✓ Administrateur '{$nom}' activé avec succès!\n";
    echo "Email: {$email}\n";
} else {
    // Créer le nouvel administrateur
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT INTO admins (nom, email, password_hash, role, actif) VALUES (?, ?, ?, ?, 1)");
    $stmt->execute([$nom, $email, $passwordHash, $role]);

    echo "✓ Administrateur principal créé avec succès!\n";
    echo str_repeat('=', 60) . "\n";
    echo "Nom: {$nom}\n";
    echo "Email: {$email}\n";
    echo "Mot de passe: {$password}\n";
    echo "Rôle: {$role}\n";
    echo str_repeat('=', 60) . "\n";
    echo "⚠ Changez ce mot de passe lors de votre première connexion!\n";
}

echo "\nConnexion : http://localhost:8000/admin/login.php\n";
