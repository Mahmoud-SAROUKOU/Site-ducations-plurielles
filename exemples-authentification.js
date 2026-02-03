// ===============================================================
// EXEMPLES D'UTILISATION - Système d'authentification dual mode
// ===============================================================

// ========== EXEMPLE 1 : CONFIGURATION INITIALE ==========

// Mode LOCAL (développement) - Rien à faire !
// Ouvrez simplement admin.html

// Mode DISTANT (Hostinger) - Configuration via console
localStorage.setItem('syncConfig', JSON.stringify({
    enabled: true,
    endpoint: 'https://votre-domaine.com/admin/api/sync.php',
    apiKey: 'k7Hx9mP2vN8qL4sT1gF6jW0zR3cY5aE8'
}));

// Recharger la page
location.reload();

// ========== EXEMPLE 2 : VÉRIFIER LE MODE ACTIF ==========

// Vérifier si on est en mode distant
console.log('Mode distant :', isOnline());
// → true si syncConfig configuré, false sinon

// Obtenir l'URL de l'API d'authentification
console.log('URL Auth :', getAuthUrl());
// → https://votre-domaine.com/admin/api/auth.php (si distant)
// → null (si local)

// Obtenir la clé API
console.log('Clé API :', getApiKey());
// → "k7Hx9mP..." (si configuré)
// → "" (si non configuré)

// ========== EXEMPLE 3 : CONNEXION PROGRAMMATIQUE ==========

// Mode LOCAL
async function loginLocal() {
    try {
        const session = await AdminSession.create(
            'admin@educationsplurielles.local',
            'Admin Local',
            '' // Pas de mot de passe pour super-admin
        );
        console.log('✅ Connexion locale réussie:', session);
        // { email, name, token, mode: 'local', expiresAt }
    } catch (error) {
        console.error('❌ Erreur:', error.message);
    }
}

// Mode DISTANT
async function loginDistant() {
    try {
        const session = await AdminSession.create(
            'admin@educationsplurielles.local',
            '', // Le nom sera récupéré depuis la DB
            '' // Pas de mot de passe pour super-admin
        );
        console.log('✅ Connexion distante réussie:', session);
        // { email, name, role, token, mode: 'distant', expiresAt }
    } catch (error) {
        console.error('❌ Erreur:', error.message);
    }
}

// Mode AUTO (détecte automatiquement)
async function loginAuto(email, password) {
    if (isOnline()) {
        console.log('📡 Connexion en mode distant...');
        await AdminSession.create(email, '', password);
    } else {
        console.log('💻 Connexion en mode local...');
        if (!AdminUsers.verify(email, password)) {
            throw new Error('Identifiants invalides');
        }
        const user = AdminUsers.findByEmail(email);
        await AdminSession.create(user.email, user.name, '');
    }
}

// ========== EXEMPLE 4 : VÉRIFIER SESSION ACTIVE ==========

async function checkCurrentSession() {
    const session = await AdminSession.get();

    if (session) {
        console.log('✅ Session active:', session);
        console.log('   Email:', session.email);
        console.log('   Nom:', session.name);
        console.log('   Mode:', session.mode);
        console.log('   Expire:', new Date(session.expiresAt).toLocaleString());

        // En mode distant, la session est vérifiée avec le serveur
        if (session.mode === 'distant') {
            console.log('   Token validé avec serveur MySQL');
        }
    } else {
        console.log('❌ Aucune session active');
    }

    return session;
}

// ========== EXEMPLE 5 : DÉCONNEXION ==========

async function logout() {
    const session = await AdminSession.get();

    if (session) {
        console.log('🚪 Déconnexion...');
        console.log('   Mode:', session.mode);

        if (session.mode === 'distant') {
            console.log('   → Suppression session MySQL');
        }

        await AdminSession.destroy();
        console.log('✅ Déconnexion réussie');
    } else {
        console.log('ℹ️ Déjà déconnecté');
    }

    // Recharger la page
    location.reload();
}

// ========== EXEMPLE 6 : CRÉER UN NOUVEL ADMIN (MODE DISTANT) ==========

async function createNewAdmin(email, name, password, role = 'admin') {
    if (!isOnline()) {
        console.error('❌ Mode distant requis pour créer un admin sur le serveur');
        return;
    }

    const authUrl = getAuthUrl();

    try {
        const response = await fetch(authUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Admin-Sync-Key': getApiKey()
            },
            body: JSON.stringify({
                action: 'create_user',
                email: email,
                name: name,
                password: password,
                role: role
            })
        });

        const data = await response.json();

        if (data.success) {
            console.log('✅ Admin créé avec succès:', data);
            console.log('   ID:', data.id);
            console.log('   Email:', email);
        } else {
            console.error('❌ Erreur:', data.error);
        }

        return data;
    } catch (error) {
        console.error('❌ Erreur réseau:', error);
    }
}

// Exemple d'utilisation
createNewAdmin(
    'nouveau@exemple.com',
    'Nouvel Administrateur',
    'password123',
    'admin'
);

// ========== EXEMPLE 7 : TESTER LA CONNEXION API ==========

async function testApiConnection() {
    if (!isOnline()) {
        console.log('⚠️ Mode local - Pas d\'API à tester');
        return;
    }

    const authUrl = getAuthUrl();
    console.log('🔍 Test de connexion API...');
    console.log('   URL:', authUrl);

    try {
        // Test simple : envoyer une requête invalide pour voir si l'API répond
        const response = await fetch(authUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Admin-Sync-Key': getApiKey()
            },
            body: JSON.stringify({ action: 'test' })
        });

        console.log('   Status:', response.status);

        const data = await response.json();
        console.log('   Réponse:', data);

        if (response.ok || data.error) {
            console.log('✅ API accessible');
        } else {
            console.log('⚠️ API répond mais avec un statut inhabituel');
        }
    } catch (error) {
        console.error('❌ API inaccessible:', error.message);
    }
}

// ========== EXEMPLE 8 : SURVEILLANCE SESSION ==========

// Vérifier la session toutes les 5 minutes
setInterval(async () => {
    const session = await AdminSession.get();

    if (!session) {
        console.warn('⚠️ Session expirée - Redirection vers login');
        location.reload();
    } else {
        console.log('✅ Session toujours active');
        console.log('   Expire dans:', Math.floor((session.expiresAt - Date.now()) / 1000 / 60), 'minutes');

        // Rafraîchir la session si proche de l'expiration
        const timeLeft = session.expiresAt - Date.now();
        if (timeLeft < 60 * 60 * 1000) { // Moins d'1h
            await AdminSession.refresh();
            console.log('🔄 Session rafraîchie');
        }
    }
}, 5 * 60 * 1000); // Toutes les 5 minutes

// ========== EXEMPLE 9 : DIAGNOSTIC COMPLET ==========

async function diagnosticAuth() {
    console.log('═══════════════════════════════════════');
    console.log('🔍 DIAGNOSTIC SYSTÈME D\'AUTHENTIFICATION');
    console.log('═══════════════════════════════════════');

    // 1. Configuration
    console.log('\n1️⃣ Configuration:');
    const config = JSON.parse(localStorage.getItem('syncConfig') || '{}');
    console.log('   syncConfig:', config.enabled ? '✅ Activé' : '❌ Désactivé');
    console.log('   Endpoint:', config.endpoint || '❌ Non configuré');
    console.log('   Clé API:', config.apiKey ? '✅ Configurée (' + config.apiKey.length + ' chars)' : '❌ Non configurée');

    // 2. Mode détecté
    console.log('\n2️⃣ Mode détecté:');
    const mode = isOnline() ? 'DISTANT (Hostinger)' : 'LOCAL (Hors ligne)';
    console.log('   Mode:', mode);
    console.log('   URL Auth:', getAuthUrl() || 'N/A');

    // 3. Session active
    console.log('\n3️⃣ Session:');
    const session = await AdminSession.get();
    if (session) {
        console.log('   ✅ Session active');
        console.log('   Email:', session.email);
        console.log('   Nom:', session.name);
        console.log('   Mode session:', session.mode);
        console.log('   Token:', session.token.substring(0, 20) + '...');
        console.log('   Expire:', new Date(session.expiresAt).toLocaleString());
    } else {
        console.log('   ❌ Aucune session');
    }

    // 4. Test API (si distant)
    if (isOnline()) {
        console.log('\n4️⃣ Test API:');
        await testApiConnection();
    }

    // 5. localStorage
    console.log('\n5️⃣ localStorage:');
    console.log('   ep_admin_session:', localStorage.getItem('ep_admin_session') ? '✅ Présent' : '❌ Absent');
    console.log('   ep_admin_users:', localStorage.getItem('ep_admin_users') ? '✅ Présent' : '❌ Absent');
    console.log('   syncConfig:', localStorage.getItem('syncConfig') ? '✅ Présent' : '❌ Absent');

    console.log('\n═══════════════════════════════════════');
}

// Lancer le diagnostic
diagnosticAuth();

// ========== EXEMPLE 10 : BASCULER ENTRE MODES ==========

// Passer en mode DISTANT
function enableDistantMode(domain, apiKey) {
    localStorage.setItem('syncConfig', JSON.stringify({
        enabled: true,
        endpoint: `https://${domain}/admin/api/sync.php`,
        apiKey: apiKey
    }));
    console.log('✅ Mode distant activé');
    console.log('   Rechargez la page pour appliquer');
}

// Passer en mode LOCAL
function enableLocalMode() {
    localStorage.removeItem('syncConfig');
    // Ou :
    // localStorage.setItem('syncConfig', JSON.stringify({ enabled: false }));

    console.log('✅ Mode local activé');
    console.log('   Rechargez la page pour appliquer');
}

// Exemples d'utilisation :
// enableDistantMode('votre-domaine.com', 'votre_cle_api');
// enableLocalMode();

// ========== EXEMPLE 11 : GESTION D'ERREURS ==========

async function loginWithErrorHandling(email, password) {
    try {
        // Tentative de connexion
        await loginAuto(email, password);

        // Succès
        console.log('✅ Connexion réussie !');
        showAdminInterface();

    } catch (error) {
        // Gestion des erreurs spécifiques
        if (error.message.includes('Clé de synchronisation invalide')) {
            console.error('❌ Erreur de configuration : Vérifiez votre clé API');
            alert('Erreur de configuration. Contactez l\'administrateur.');

        } else if (error.message.includes('Email ou mot de passe incorrect')) {
            console.error('❌ Identifiants invalides');
            alert('Email ou mot de passe incorrect. Veuillez réessayer.');

        } else if (error.message.includes('fetch')) {
            console.error('❌ Erreur réseau : Impossible de contacter le serveur');
            alert('Erreur de connexion au serveur. Vérifiez votre connexion internet.');

        } else {
            console.error('❌ Erreur inconnue:', error);
            alert('Une erreur est survenue. Veuillez réessayer.');
        }
    }
}

// ========== EXEMPLE 12 : MIDDLEWARE DE PROTECTION ==========

// Fonction pour protéger une page/fonction
async function requireAuth(callback) {
    const session = await AdminSession.get();

    if (!session) {
        console.warn('⚠️ Accès refusé : Session expirée');
        alert('Votre session a expiré. Veuillez vous reconnecter.');
        location.reload();
        return;
    }

    // Session valide : exécuter le callback
    callback(session);
}

// Utilisation
requireAuth(async (session) => {
    console.log('✅ Accès autorisé pour:', session.name);

    // Code protégé ici
    // ...
});

// ========== EXEMPLE 13 : LOGGER LES ACTIONS ==========

// Logger toutes les actions d'authentification
const authLogger = {
    log: function (action, details) {
        const timestamp = new Date().toISOString();
        const mode = isOnline() ? 'DISTANT' : 'LOCAL';

        console.log(`[${timestamp}] [${mode}] ${action}`);
        if (details) {
            console.log('   Détails:', details);
        }

        // Optionnel : sauvegarder dans localStorage
        const logs = JSON.parse(localStorage.getItem('auth_logs') || '[]');
        logs.push({ timestamp, mode, action, details });

        // Garder seulement les 100 derniers logs
        if (logs.length > 100) {
            logs.shift();
        }

        localStorage.setItem('auth_logs', JSON.stringify(logs));
    },

    getLogs: function () {
        return JSON.parse(localStorage.getItem('auth_logs') || '[]');
    },

    clearLogs: function () {
        localStorage.removeItem('auth_logs');
        console.log('✅ Logs effacés');
    }
};

// Utilisation
authLogger.log('LOGIN_ATTEMPT', { email: 'admin@exemple.com' });
authLogger.log('LOGIN_SUCCESS', { email: 'admin@exemple.com', mode: 'distant' });
authLogger.log('LOGOUT', { email: 'admin@exemple.com' });

// Voir tous les logs
console.table(authLogger.getLogs());

// ========== EXEMPLE 14 : HOOK PERSONNALISÉ ==========

// Créer un hook qui s'exécute après chaque connexion réussie
window.onAuthSuccess = async function (session) {
    console.log('🎉 Hook onAuthSuccess déclenché');
    console.log('   Session:', session);

    // Actions personnalisées
    authLogger.log('LOGIN_SUCCESS', {
        email: session.email,
        mode: session.mode
    });

    // Charger les données utilisateur
    if (session.mode === 'distant') {
        console.log('📡 Chargement données depuis serveur...');
        // await loadUserData();
    }

    // Analytics
    if (window.gtag) {
        gtag('event', 'login', {
            method: session.mode
        });
    }
};

// Modifier initLoginSystem() pour appeler le hook
// Après AdminSession.create() réussi :
// if (window.onAuthSuccess) await window.onAuthSuccess(session);

// ===============================================================
// FIN DES EXEMPLES
// ===============================================================

/**
 * NOTES IMPORTANTES :
 * 
 * 1. Mode LOCAL vs DISTANT :
 *    - LOCAL : localStorage uniquement, pas de serveur
 *    - DISTANT : MySQL + API, session vérifiée côté serveur
 * 
 * 2. Super-admin :
 *    - Email : admin@educationsplurielles.local
 *    - Pas de mot de passe requis
 *    - Créé automatiquement
 * 
 * 3. Sécurité :
 *    - Clé API : 32+ caractères aléatoires
 *    - Tokens : 64 chars hex en mode distant
 *    - Password : bcrypt en base de données
 * 
 * 4. Sessions :
 *    - Durée : 24 heures
 *    - Vérifiées à chaque get() en mode distant
 *    - Nettoyées automatiquement si expirées
 * 
 * 5. Configuration :
 *    - syncConfig contrôle tout
 *    - Aucune modification de code nécessaire
 *    - Détection automatique du mode
 */
