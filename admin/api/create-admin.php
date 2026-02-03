<?php

/**
 * API - Créer le premier administrateur
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../db.php';

try {
    // Vérifier que la requête est en POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Méthode non autorisée');
    }

    $pdo = Database::connect();

    // Vérifier qu'il n'y a pas déjà d'admin
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM admins");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['count'] > 0) {
        throw new Exception('Un administrateur existe déjà. Utilisez le formulaire de connexion.');
    }

    // Récupérer les données du formulaire
    $nom = trim($_POST['nom'] ?? '');
    $email = trim(strtolower($_POST['email'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    // Validation
    if (empty($nom) || empty($email) || empty($password)) {
        throw new Exception('Tous les champs sont requis');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Email invalide');
    }

    if (strlen($password) < 6) {
        throw new Exception('Le mot de passe doit contenir au moins 6 caractères');
    }

    // Hasher le mot de passe
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    // Insérer le nouvel admin avec le rôle super_admin
    $stmt = $pdo->prepare(
        "INSERT INTO admins (nom, email, password_hash, role, created_at) 
         VALUES (?, ?, ?, 'super_admin', NOW())"
    );

    $stmt->execute([$nom, $email, $passwordHash]);

    echo json_encode([
        'success' => true,
        'message' => 'Administrateur créé avec succès',
        'admin' => [
            'id' => $pdo->lastInsertId(),
            'nom' => $nom,
            'email' => $email,
            'role' => 'super_admin'
        ]
    ]);
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate') !== false) {
        echo json_encode([
            'success' => false,
            'message' => 'Cet email existe déjà'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Erreur lors de la création: ' . $e->getMessage()
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
