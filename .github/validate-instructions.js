#!/usr/bin/env node
/**
 * Test de validation des instructions AI
 * Vérifie que tous les liens et références sont valides
 */

const fs = require('fs');
const path = require('path');

console.log('🔍 Validation des instructions AI...\n');

const instructionsPath = path.join(__dirname, 'copilot-instructions.md');
const rootDir = path.join(__dirname, '..');

// Lire le fichier
const content = fs.readFileSync(instructionsPath, 'utf-8');

// Extraire tous les liens markdown relatifs
const linkRegex = /\[([^\]]+)\]\(\.\.\/([^)]+)\)/g;
let match;
const links = [];

while ((match = linkRegex.exec(content)) !== null) {
    links.push({ text: match[1], path: match[2] });
}

console.log(`📋 ${links.length} liens trouvés\n`);

// Vérifier chaque lien
let errors = 0;
let warnings = 0;

links.forEach(link => {
    const fullPath = path.join(rootDir, link.path);

    if (fs.existsSync(fullPath)) {
        console.log(`✅ ${link.text}`);
    } else {
        console.log(`❌ ${link.text} - Fichier introuvable: ${link.path}`);
        errors++;
    }
});

console.log('\n' + '='.repeat(50));

// Vérifier les patterns de code
const codeBlockRegex = /```(\w+)\n([\s\S]*?)```/g;
const codeBlocks = [];

while ((match = codeBlockRegex.exec(content)) !== null) {
    codeBlocks.push({ lang: match[1], code: match[2] });
}

console.log(`\n📝 ${codeBlocks.length} exemples de code trouvés`);

// Vérifier syntaxe PHP
const phpBlocks = codeBlocks.filter(b => b.lang === 'php');
console.log(`   - PHP: ${phpBlocks.length}`);

// Vérifier syntaxe JavaScript
const jsBlocks = codeBlocks.filter(b => ['javascript', 'js'].includes(b.lang));
console.log(`   - JavaScript: ${jsBlocks.length}`);

// Vérifier syntaxe PowerShell
const psBlocks = codeBlocks.filter(b => ['powershell', 'ps1', 'bash'].includes(b.lang));
console.log(`   - Shell: ${psBlocks.length}`);

console.log('\n' + '='.repeat(50));

// Résumé
if (errors === 0) {
    console.log('\n✅ Validation réussie ! Toutes les références sont valides.');
    process.exit(0);
} else {
    console.log(`\n❌ ${errors} erreur(s) trouvée(s). Veuillez corriger les liens.`);
    process.exit(1);
}
