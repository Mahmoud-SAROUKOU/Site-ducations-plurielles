<?php
require_once 'admin/db.php';

$pdo = Database::connect();
$stmt = $pdo->query('SELECT nom, email, role, actif FROM admins');

echo "Liste des administrateurs:\n";
echo str_repeat('=', 60) . "\n";

while ($row = $stmt->fetch()) {
    echo sprintf(
        "%-25s %-30s %-15s %s\n",
        $row['nom'],
        $row['email'],
        $row['role'],
        $row['actif'] ? '✓ Actif' : '✗ Inactif'
    );
}
