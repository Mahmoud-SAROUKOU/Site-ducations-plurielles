/**
 * 🆘 SCRIPT DE RÉCUPÉRATION D'URGENCE - Administrateurs
 * 
 * Ouvrez la console du navigateur (F12) dans admin.html
 * Collez ce script entièrement et appuyez sur Entrée
 */

(function () {
    console.log('🆘 SCRIPT DE RÉCUPÉRATION D\'URGENCE');
    console.log('='.repeat(50));

    // ===== ÉTAPE 1: Diagnostic =====
    console.log('\n1️⃣ DIAGNOSTIC...\n');

    const adminsLS = JSON.parse(localStorage.getItem('ep_admins') || '[]');
    const adminsCurrent = window.admins || [];
    const syncConfig = JSON.parse(localStorage.getItem('syncConfig') || '{}');

    console.table({
        'Admins en localStorage': adminsLS.length,
        'Admins en mémoire': adminsCurrent.length,
        'Sync activée': syncConfig.enabled || false,
        'Endpoint configuré': syncConfig.endpoint ? '✅' : '❌'
    });

    // ===== ÉTAPE 2: Résoudre les divergences =====
    console.log('\n2️⃣ SYNCHRONISATION LOCALE...\n');

    if (adminsLS.length > adminsCurrent.length) {
        console.warn('⚠️ Le localStorage a plus d\'admins que la mémoire!');
        console.log('📋 Admins en localStorage:', adminsLS);
        console.log('📋 Admins en mémoire:', adminsCurrent);

        // Utiliser le localStorage comme source de vérité
        window.admins = adminsLS;
        console.log('✅ Mémoire restaurée depuis localStorage');
    } else if (adminsCurrent.length > adminsLS.length) {
        console.warn('⚠️ La mémoire a plus d\'admins que localStorage!');
        console.log('Sauvegarde automatique...');
        localStorage.setItem('ep_admins', JSON.stringify(adminsCurrent));
        console.log('✅ localStorage restauré depuis mémoire');
    }

    // ===== ÉTAPE 3: Nettoyer les données corrompues =====
    console.log('\n3️⃣ NETTOYAGE...\n');

    let cleaned = false;
    window.admins = window.admins.filter(admin => {
        if (!admin.id || !admin.name || !admin.email) {
            console.warn('❌ Admin corrompu supprimé:', admin);
            cleaned = true;
            return false;
        }
        return true;
    });

    if (cleaned) {
        localStorage.setItem('ep_admins', JSON.stringify(window.admins));
        console.log('✅ Données corrompues nettoyées');
    }

    // ===== ÉTAPE 4: Ajouter l'admin par défaut s'il manque =====
    console.log('\n4️⃣ VÉRIFICATION ADMIN PAR DÉFAUT...\n');

    if (window.admins.length === 0) {
        const defaultAdmin = {
            id: Date.now(),
            name: 'Admin',
            email: 'admin@local.com',
            role: 'super_admin',
            created_at: new Date().toISOString(),
            needs_sync: false
        };
        window.admins.push(defaultAdmin);
        localStorage.setItem('ep_admins', JSON.stringify(window.admins));
        console.log('✅ Admin par défaut créé:', defaultAdmin);
    } else {
        console.log('✅ Des administrateurs existent déjà');
    }

    // ===== ÉTAPE 5: Rerender =====
    console.log('\n5️⃣ RAFRAÎCHISSEMENT...\n');

    if (window.renderAdmins) {
        window.renderAdmins();
        console.log('✅ Affichage des administrateurs rafraîchi');
    }

    if (window.updateStats) {
        window.updateStats();
        console.log('✅ Statistiques mises à jour');
    }

    // ===== RAPPORT FINAL =====
    console.log('\n' + '='.repeat(50));
    console.log('✨ RAPPORT FINAL\n');
    console.table({
        'Administrateurs totals': window.admins.length,
        'En localStorage': JSON.parse(localStorage.getItem('ep_admins') || '[]').length,
        'Sync activée': syncConfig.enabled || false,
        'État': 'STABLE'
    });

    console.log('\n🎯 Actions recommandées:');
    console.log('1. Recharger la page (F5)');
    console.log('2. Vérifier que les administrateurs s\'affichent');
    console.log('3. Dans Paramètres, vérifier la config de sync');

    if (!syncConfig.enabled) {
        console.log('\n⚠️  NOTE: La synchronisation est DÉSACTIVÉE (normal pour localhost)');
        console.log('   Pour l\'activer, allez dans Paramètres et cochez "Synchroniser en ligne"');
    }

    console.log('\n' + '='.repeat(50) + '\n');
})();
