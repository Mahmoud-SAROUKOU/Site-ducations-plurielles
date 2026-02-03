# Script de connexion GitHub - Educations Plurielles
$ErrorActionPreference = "Stop"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "CONNEXION PROJET -> GITHUB" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Chemin Git
$gitPath = "C:\Program Files\Git\bin\git.exe"

if (-not (Test-Path $gitPath)) {
    Write-Host "Git n'est pas installe a l'emplacement attendu" -ForegroundColor Red
    Write-Host "Verifiez que Git est bien installe" -ForegroundColor Yellow
    Read-Host "Appuyez sur Entree"
    exit 1
}

Write-Host "Git trouve: $gitPath" -ForegroundColor Green
Write-Host ""

# Vérifier si dépôt existe
if (Test-Path ".git") {
    Write-Host "Un depot Git existe deja!" -ForegroundColor Yellow
    $response = Read-Host "Voulez-vous le reinitialiser? (O/N)"
    if ($response -eq "O" -or $response -eq "o") {
        Remove-Item -Recurse -Force .git
        Write-Host "Depot supprime" -ForegroundColor Green
    }
    else {
        Write-Host "On continue avec le depot existant..." -ForegroundColor Yellow
    }
}

# Initialiser le dépôt
if (-not (Test-Path ".git")) {
    Write-Host "Initialisation du depot Git..." -ForegroundColor Yellow
    & $gitPath init
    & $gitPath branch -M main
    Write-Host "Depot initialise!" -ForegroundColor Green
    Write-Host ""
}

# Configuration utilisateur
Write-Host "Configuration Git..." -ForegroundColor Yellow
Write-Host "Nom actuel: " -NoNewline
& $gitPath config user.name
Write-Host "Email actuel: " -NoNewline
& $gitPath config user.email
Write-Host ""

$changeConfig = Read-Host "Modifier la configuration? (O/N)"
if ($changeConfig -eq "O" -or $changeConfig -eq "o") {
    $userName = Read-Host "Votre nom"
    $userEmail = Read-Host "Votre email"
    & $gitPath config user.name "$userName"
    & $gitPath config user.email "$userEmail"
    Write-Host "Configuration mise a jour!" -ForegroundColor Green
    Write-Host ""
}

# Vérifier .gitignore
if (-not (Test-Path ".gitignore")) {
    Write-Host "ATTENTION: .gitignore manquant!" -ForegroundColor Red
    Write-Host "Fichiers sensibles non proteges!" -ForegroundColor Red
    Read-Host "Appuyez sur Entree pour continuer quand meme"
}
else {
    Write-Host ".gitignore trouve - Fichiers sensibles proteges" -ForegroundColor Green
    Write-Host ""
}

# Ajouter les fichiers
Write-Host "Ajout des fichiers au depot..." -ForegroundColor Yellow
& $gitPath add .
Write-Host "Fichiers ajoutes!" -ForegroundColor Green
Write-Host ""

# Premier commit
Write-Host "Creation du premier commit..." -ForegroundColor Yellow
$commitMessage = "Initial commit: Educations Plurielles v1.1 avec admin panel et sync Hostinger"
& $gitPath commit -m $commitMessage
Write-Host "Commit cree!" -ForegroundColor Green
Write-Host ""

# Statut
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "DEPOT LOCAL PRET!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "Statut du depot:" -ForegroundColor Yellow
& $gitPath status
Write-Host ""

Write-Host "Historique:" -ForegroundColor Yellow
& $gitPath log --oneline
Write-Host ""

# Instructions GitHub
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "ETAPE 2: CREER LE DEPOT GITHUB" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "1. Allez sur: https://github.com/new" -ForegroundColor White
Write-Host ""
Write-Host "2. Remplissez:" -ForegroundColor White
Write-Host "   - Nom: educations-plurielles" -ForegroundColor Gray
Write-Host "   - Description: Site Educations Plurielles avec admin v1.1" -ForegroundColor Gray
Write-Host "   - Visibilite: Private (recommande)" -ForegroundColor Gray
Write-Host "   - Ne PAS cocher 'Initialize with README'" -ForegroundColor Gray
Write-Host ""
Write-Host "3. Cliquez: Create repository" -ForegroundColor White
Write-Host ""
Write-Host "4. Copiez l'URL qui s'affiche" -ForegroundColor White
Write-Host "   Format: https://github.com/USERNAME/educations-plurielles.git" -ForegroundColor Gray
Write-Host ""

$githubUrl = Read-Host "Collez l'URL de votre depot GitHub (ou ENTREE pour plus tard)"

if ($githubUrl -and $githubUrl -ne "") {
    Write-Host ""
    Write-Host "Connexion au depot distant..." -ForegroundColor Yellow
    
    # Vérifier si remote existe
    $remotes = & $gitPath remote
    if ($remotes -contains "origin") {
        Write-Host "Remote 'origin' existe deja, mise a jour..." -ForegroundColor Yellow
        & $gitPath remote set-url origin $githubUrl
    }
    else {
        & $gitPath remote add origin $githubUrl
    }
    
    Write-Host "Remote configure!" -ForegroundColor Green
    Write-Host ""
    
    Write-Host "Push vers GitHub..." -ForegroundColor Yellow
    Write-Host "(Entrez vos identifiants GitHub si demande)" -ForegroundColor Gray
    Write-Host ""
    
    try {
        & $gitPath push -u origin main
        Write-Host ""
        Write-Host "========================================" -ForegroundColor Cyan
        Write-Host "SUCCES! CODE SUR GITHUB!" -ForegroundColor Green
        Write-Host "========================================" -ForegroundColor Cyan
        Write-Host ""
        Write-Host "Votre projet est maintenant sur GitHub:" -ForegroundColor Green
        Write-Host $githubUrl -ForegroundColor White
        Write-Host ""
    }
    catch {
        Write-Host ""
        Write-Host "Erreur lors du push" -ForegroundColor Red
        Write-Host "Causes possibles:" -ForegroundColor Yellow
        Write-Host "- Identifiants incorrects" -ForegroundColor Gray
        Write-Host "- Connexion internet" -ForegroundColor Gray
        Write-Host "- URL incorrecte" -ForegroundColor Gray
        Write-Host ""
        Write-Host "Reessayez avec:" -ForegroundColor Yellow
        Write-Host "git push -u origin main" -ForegroundColor White
        Write-Host ""
    }
}
else {
    Write-Host ""
    Write-Host "========================================" -ForegroundColor Cyan
    Write-Host "DEPOT LOCAL PRET" -ForegroundColor Yellow
    Write-Host "========================================" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "Pour connecter plus tard, executez:" -ForegroundColor Yellow
    Write-Host "git remote add origin https://github.com/USERNAME/educations-plurielles.git" -ForegroundColor White
    Write-Host "git push -u origin main" -ForegroundColor White
    Write-Host ""
}

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "COMMANDES UTILES" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Sauvegarder modifications:" -ForegroundColor Yellow
Write-Host "  git add ." -ForegroundColor White
Write-Host "  git commit -m `"message`"" -ForegroundColor White
Write-Host "  git push" -ForegroundColor White
Write-Host ""
Write-Host "Recuperer modifications:" -ForegroundColor Yellow
Write-Host "  git pull" -ForegroundColor White
Write-Host ""
Write-Host "Voir statut:" -ForegroundColor Yellow
Write-Host "  git status" -ForegroundColor White
Write-Host ""

Read-Host "Appuyez sur Entree pour fermer"
