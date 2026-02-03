<?php

/**
 * ENVOIE D'EMAIL POUR ADMINISTRATEUR
 * Endpoint qui envoie un email avec les identifiants à un nouvel admin
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Admin-Key');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// Récupérer les données
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
    exit;
}

$email = trim($input['email'] ?? '');
$name = trim($input['name'] ?? '');
$password = trim($input['password'] ?? '');
$loginUrl = trim($input['loginUrl'] ?? 'https://votre-site.com/admin/login-unified.php');

// Validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !$name || !$password) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
    exit;
}

// Configuration email
$fromEmail = 'admin@educationsplurielles.fr';
$fromName = 'Éducations Plurielles - Admin';
$subject = 'Accès administrateur Éducations Plurielles';

// Corps de l'email (HTML)
$emailBody = <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
        .content { background: #f5f5f5; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .credentials { background: white; padding: 15px; border-left: 4px solid #1e3a8a; margin: 15px 0; font-family: monospace; }
        .button { display: inline-block; background: #1e3a8a; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; margin: 15px 0; }
        .footer { font-size: 12px; color: #999; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Bienvenue sur Éducations Plurielles</h1>
            <p>Votre compte administrateur a été créé</p>
        </div>

        <div class="content">
            <p>Bonjour <strong>{$name}</strong>,</p>

            <p>Un compte administrateur a été créé pour vous sur la plateforme <strong>Éducations Plurielles</strong>.</p>

            <h3 style="color: #1e3a8a; margin-top: 25px;">Vos identifiants de connexion :</h3>
            
            <div class="credentials">
                <div><strong>Email :</strong> {$email}</div>
                <div><strong>Mot de passe :</strong> {$password}</div>
            </div>

            <p style="background: #fff3cd; padding: 15px; border-radius: 6px; border-left: 4px solid #ffc107;">
                <strong>⚠️ Important :</strong> Conservez ces identifiants en sécurité. Nous vous conseillons de changer votre mot de passe à la première connexion.
            </p>

            <a href="{$loginUrl}" class="button" style="display: block; text-align: center; width: 100%; box-sizing: border-box;">
                Se connecter maintenant
            </a>

            <h3 style="color: #1e3a8a; margin-top: 25px;">Prochaines étapes :</h3>
            <ol>
                <li>Connectez-vous avec vos identifiants ci-dessus</li>
                <li>Changez votre mot de passe dans les paramètres</li>
                <li>Commencez à gérer le contenu de la plateforme</li>
            </ol>

            <p style="margin-top: 25px; color: #666;">
                Si vous avez des questions, veuillez contacter l'administrateur principal.
            </p>
        </div>

        <div class="footer">
            <p>Éducations Plurielles © 2026 - Tous droits réservés</p>
            <p>Cet email a été envoyé automatiquement. Veuillez ne pas répondre directement.</p>
        </div>
    </div>
</body>
</html>
HTML;

// Headers du mail
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
$headers .= "From: {$fromName} <{$fromEmail}>\r\n";
$headers .= "Reply-To: {$fromEmail}\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

// Envoyer l'email
$success = false;
$message = 'Erreur lors de l\'envoi de l\'email';

try {
    // Essayer avec mail() (fonction native PHP)
    if (function_exists('mail')) {
        $success = mail($email, $subject, $emailBody, $headers);

        if ($success) {
            $message = 'Email envoyé avec succès';
        } else {
            $message = 'Impossible d\'envoyer l\'email (mail() échoué)';
        }
    } else {
        // Si mail() n'est pas disponible, on peut essayer SMTP plus tard
        $message = 'Service email non configuré';
        $success = false;
    }

    // Optionnel : enregistrer dans les logs
    $logFile = __DIR__ . '/../../admin/emails.log';
    if (!is_dir(dirname($logFile))) {
        mkdir(dirname($logFile), 0755, true);
    }

    file_put_contents($logFile, sprintf(
        "[%s] Admin: %s <%s> - Statut: %s\n",
        date('Y-m-d H:i:s'),
        $name,
        $email,
        $success ? 'SUCCÈS' : 'ÉCHOUÉ'
    ), FILE_APPEND);
} catch (Exception $e) {
    $message = 'Exception: ' . $e->getMessage();
    error_log("Admin email error: " . $message);
}

// Répondre
http_response_code($success ? 200 : 500);
echo json_encode([
    'success' => $success,
    'message' => $message,
    'email' => $email,
    'timestamp' => date('c')
]);
