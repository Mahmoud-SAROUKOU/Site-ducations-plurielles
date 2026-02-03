<?php

/**
 * SYSTÈME D'ENVOI D'EMAIL
 * Envoi d'emails de bienvenue aux administrateurs
 */

class Mailer
{
    /**
     * Envoie un email de bienvenue à un nouvel administrateur
     */
    public static function sendWelcomeEmail(string $nom, string $email, string $password): bool
    {
        $subject = '🎉 Bienvenue sur ' . MAIL_FROM_NAME;

        $loginUrl = APP_URL . '/admin/login.php';

        $body = self::getEmailTemplate($nom, $email, $password, $loginUrl);

        // En développement : afficher l'email dans les logs
        if (!MAIL_SMTP_HOST) {
            error_log("=== EMAIL DE BIENVENUE ===\n" .
                "À: {$nom} <{$email}>\n" .
                "Sujet: {$subject}\n" .
                "---\n{$body}\n" .
                "==================\n");

            // Sauvegarder dans un fichier
            $file = __DIR__ . '/emails.log';
            $log = "\n\n" . date('Y-m-d H:i:s') . "\n";
            $log .= "À: {$nom} <{$email}>\n";
            $log .= "Sujet: {$subject}\n";
            $log .= "---\n{$body}\n";
            $log .= str_repeat('=', 60) . "\n";
            file_put_contents($file, $log, FILE_APPEND);

            return true;
        }

        // En production : envoi SMTP réel
        return self::sendSMTP($email, $nom, $subject, $body);
    }

    /**
     * Template d'email de bienvenue (style WordPress)
     */
    private static function getEmailTemplate(string $nom, string $email, string $password, string $loginUrl): string
    {
        return "Bonjour {$nom},

Bienvenue sur " . MAIL_FROM_NAME . " !

Votre compte administrateur a été créé avec succès. Vous pouvez maintenant vous connecter à l'interface d'administration avec les informations suivantes :

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
🔐 VOS IDENTIFIANTS
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Nom d'utilisateur : {$email}
Mot de passe : {$password}
URL de connexion : {$loginUrl}

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

⚠️ IMPORTANT : Pour des raisons de sécurité, nous vous recommandons fortement de changer votre mot de passe lors de votre première connexion.

Pour vous connecter :
1. Rendez-vous sur : {$loginUrl}
2. Connectez-vous avec les identifiants ci-dessus
3. Accédez aux paramètres pour changer votre mot de passe

Si vous avez des questions ou besoin d'aide, n'hésitez pas à nous contacter.

Cordialement,
L'équipe " . MAIL_FROM_NAME . "

---
Cet email a été envoyé automatiquement, merci de ne pas y répondre.
";
    }

    /**
     * Envoi via SMTP (pour production)
     */
    private static function sendSMTP(string $to, string $toName, string $subject, string $body): bool
    {
        $headers = "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM . ">\r\n";
        $headers .= "Reply-To: " . MAIL_FROM . "\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        // Utiliser mail() pour simple envoi (pour SMTP avancé, utiliser PHPMailer)
        return mail($to, $subject, $body, $headers);
    }

    /**
     * Envoie un email de notification de changement de statut
     */
    public static function sendStatusChangeEmail(string $nom, string $email, bool $actif): bool
    {
        $subject = $actif ? '✅ Votre compte a été activé' : '⚠️ Votre compte a été désactivé';

        $body = "Bonjour {$nom},\n\n";

        if ($actif) {
            $loginUrl = APP_URL . '/admin/login.php';
            $body .= "Votre compte administrateur sur " . MAIL_FROM_NAME . " a été activé.\n\n";
            $body .= "Vous pouvez maintenant vous connecter à l'adresse suivante :\n";
            $body .= $loginUrl . "\n\n";
        } else {
            $body .= "Votre compte administrateur sur " . MAIL_FROM_NAME . " a été désactivé.\n\n";
            $body .= "Si vous pensez qu'il s'agit d'une erreur, veuillez contacter l'administrateur principal.\n\n";
        }

        $body .= "Cordialement,\n";
        $body .= "L'équipe " . MAIL_FROM_NAME;

        // En développement
        if (!MAIL_SMTP_HOST) {
            error_log("Email changement statut: {$email} - " . ($actif ? 'Activé' : 'Désactivé'));
            $file = __DIR__ . '/emails.log';
            file_put_contents($file, "\n" . date('Y-m-d H:i:s') . " - {$subject} - {$email}\n", FILE_APPEND);
            return true;
        }

        return self::sendSMTP($email, $nom, $subject, $body);
    }
}
