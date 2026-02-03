<?php

/**
 * REDIRECTION vers admin.html
 * L'installation se fait maintenant via admin.html
 */

header('Location: ../admin.html');
exit;

// Étape 1 : Initialiser la DB
if ($step === 'init') {
  if (Database::init()) {
    $message = '✓ Base de données initialisée';
    $messageType = 'success';
    $step = 'admin';
  } else {
    $message = '✗ Erreur lors de l\'initialisation';
    $messageType = 'error';
  }
}

// Étape 2 : Créer le premier admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 'admin') {
  $auth = new Auth();
  $result = $auth->createAdmin(
    trim($_POST['nom'] ?? ''),
    trim($_POST['email'] ?? ''),
    trim($_POST['password'] ?? ''),
    'super_admin'
  );

  if ($result['success']) {
    $message = '✓ ' . $result['msg'];
    $messageType = 'success';
    $step = 'done';
  } else {
    $message = '✗ ' . $result['msg'];
    $messageType = 'error';
  }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Installation - Admin</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .container {
      background: white;
      border-radius: 12px;
      padding: 40px;
      max-width: 500px;
      width: 100%;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    h1 {
      color: #333;
      margin-bottom: 30px;
      text-align: center;
      font-size: 28px;
    }

    .step {
      display: none;
      animation: fadeIn 0.3s ease-in;
    }

    .step.active {
      display: block;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
      }

      to {
        opacity: 1;
      }
    }

    .form-group {
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin-bottom: 8px;
      color: #555;
      font-weight: 500;
      font-size: 14px;
    }

    input {
      width: 100%;
      padding: 12px;
      border: 2px solid #e0e0e0;
      border-radius: 6px;
      font-size: 14px;
      transition: border-color 0.3s;
    }

    input:focus {
      outline: none;
      border-color: #667eea;
      background: #f8f9ff;
    }

    .message {
      padding: 15px;
      margin-bottom: 20px;
      border-radius: 6px;
      font-size: 14px;
    }

    .message.success {
      background: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    .message.error {
      background: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }

    .button-group {
      display: flex;
      gap: 10px;
      margin-top: 25px;
    }

    button {
      flex: 1;
      padding: 12px 20px;
      border: none;
      border-radius: 6px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
    }

    .btn-primary {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
      background: #e0e0e0;
      color: #333;
    }

    .btn-secondary:hover {
      background: #d0d0d0;
    }

    .info-box {
      background: #f0f4ff;
      border-left: 4px solid #667eea;
      padding: 15px;
      margin-bottom: 25px;
      border-radius: 6px;
      font-size: 14px;
      color: #555;
      line-height: 1.6;
    }

    .success-box {
      text-align: center;
      padding: 40px 20px;
    }

    .success-icon {
      font-size: 60px;
      margin-bottom: 20px;
    }

    .success-box h2 {
      color: #155724;
      margin-bottom: 10px;
    }

    .success-box p {
      color: #666;
      margin-bottom: 25px;
    }
  </style>
</head>

<body>
  <div class="container">
    <h1>🔧 Installation</h1>

    <?php if ($message): ?>
      <div class="message <?php echo htmlspecialchars($messageType); ?>">
        <?php echo htmlspecialchars($message); ?>
      </div>
    <?php endif; ?>

    <!-- Étape 1 : Démarrage -->
    <div class="step <?php echo ($step === 'start') ? 'active' : ''; ?>">
      <div class="info-box">
        <strong>Bienvenue ! 👋</strong><br><br>
        Cette page initialise votre système admin. Trois étapes simples :<br>
        1. Initialiser la base de données<br>
        2. Créer le premier administrateur<br>
        3. C'est prêt !
      </div>
      <div class="button-group">
        <a href="?step=init" style="flex: 1;">
          <button type="button" class="btn-primary" style="width: 100%;">Démarrer l'installation</button>
        </a>
      </div>
    </div>

    <!-- Étape 2 : Créer admin -->
    <div class="step <?php echo ($step === 'admin') ? 'active' : ''; ?>">
      <div class="info-box">
        <strong>Créer le premier administrateur</strong><br>
        Cet utilisateur aura tous les droits sur le système.
      </div>
      <form method="POST">
        <div class="form-group">
          <label>Nom complet *</label>
          <input type="text" name="nom" required placeholder="Ex: Jean Dupont">
        </div>
        <div class="form-group">
          <label>Email *</label>
          <input type="email" name="email" required placeholder="Ex: admin@example.com">
        </div>
        <div class="form-group">
          <label>Mot de passe *</label>
          <input type="password" name="password" required placeholder="Min. 6 caractères" minlength="6">
        </div>
        <div class="button-group">
          <button type="submit" class="btn-primary">Créer l'admin</button>
        </div>
      </form>
    </div>

    <!-- Étape 3 : Succès -->
    <div class="step <?php echo ($step === 'done') ? 'active' : ''; ?>">
      <div class="success-box">
        <div class="success-icon">✨</div>
        <h2>Installation réussie !</h2>
        <p>Votre système admin est maintenant opérationnel.</p>
        <a href="login.php" style="text-decoration: none;">
          <button type="button" class="btn-primary" style="width: 100%;">Se connecter</button>
        </a>
      </div>
    </div>
  </div>
</body>

</html>