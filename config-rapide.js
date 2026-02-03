/**
 * 🚀 ASSISTANT DE CONFIGURATION RAPIDE
 * 
 * Copiez ce code dans la console de votre navigateur (F12)
 * pendant que admin.html est ouvert pour configurer rapidement
 */

(function () {
    console.log('🔧 Assistant de configuration Éducations Plurielles');
    console.log('─'.repeat(50));

    // ===== CONFIGURATION À PERSONNALISER =====
    const CONFIG = {
        // Remplacez par votre domaine Hostinger
        domain: 'votre-domaine.com',

        // Remplacez par votre clé sécurisée (même que dans PHP)
        apiKey: 'votre_cle_secrete_unique',

        // Activer la synchronisation automatiquement (true/false)
        enableSync: false
    };
    // =========================================

    const syncConfig = {
        enabled: CONFIG.enableSync,
        endpoint: `https://${CONFIG.domain}/admin/api/sync.php`,
        apiKey: CONFIG.apiKey,
        refreshUrl: `https://${CONFIG.domain}/?refresh=1`,
        uploadUrl: `https://${CONFIG.domain}/admin/api/upload.php`
    };

    // Sauvegarde dans localStorage
    localStorage.setItem('syncConfig', JSON.stringify(syncConfig));

    console.log('✅ Configuration enregistrée:');
    console.table({
        'Domaine': CONFIG.domain,
        'Synchronisation': CONFIG.enableSync ? '✅ Activée' : '❌ Désactivée',
        'Endpoint sync': syncConfig.endpoint,
        'Endpoint upload': syncConfig.uploadUrl,
        'Refresh URL': syncConfig.refreshUrl,
        'Clé API': CONFIG.apiKey ? '✅ Définie' : '❌ Manquante'
    });

    console.log('─'.repeat(50));
    console.log('💡 Prochaines étapes:');
    console.log('1. Rechargez la page (F5)');
    console.log('2. Allez dans Paramètres (⚙️)');
    console.log('3. Vérifiez que les champs sont remplis');
    console.log('4. Cochez "Synchroniser en ligne" si prêt');
    console.log('5. Cliquez sur "💾 Enregistrer la synchro"');
    console.log('─'.repeat(50));
})();
