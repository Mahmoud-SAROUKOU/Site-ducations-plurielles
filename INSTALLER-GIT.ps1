# Installation automatique de Git pour Windows
Write-Host "Installation de Git pour Windows..." -ForegroundColor Cyan

$gitInstalled = Get-Command git -ErrorAction SilentlyContinue
if ($gitInstalled) {
    Write-Host "Git est deja installe: $(git --version)" -ForegroundColor Green
    $response = Read-Host "Reinstaller? (O/N)"
    if ($response -ne "O") { exit }
}

$gitUrl = "https://github.com/git-for-windows/git/releases/download/v2.43.0.windows.1/Git-2.43.0-64-bit.exe"
$installerPath = "$env:TEMP\GitInstaller.exe"

Write-Host "Telechargement..." -ForegroundColor Yellow
Invoke-WebRequest -Uri $gitUrl -OutFile $installerPath -UseBasicParsing

Write-Host "Installation..." -ForegroundColor Yellow
Start-Process -FilePath $installerPath -ArgumentList "/VERYSILENT", "/NORESTART" -Wait

Remove-Item $installerPath -Force -ErrorAction SilentlyContinue

Write-Host "GIT INSTALLE!" -ForegroundColor Green
Write-Host ""
Write-Host "Configuration:" -ForegroundColor Yellow
$userName = Read-Host "Votre nom"
$userEmail = Read-Host "Votre email"

& 'C:\Program Files\Git\bin\git.exe' config --global user.name "$userName"
& 'C:\Program Files\Git\bin\git.exe' config --global user.email "$userEmail"
& 'C:\Program Files\Git\bin\git.exe' config --global init.defaultBranch main

Write-Host "Configuration enregistree!" -ForegroundColor Green
Write-Host ""
Write-Host "Prochaines etapes:" -ForegroundColor Yellow
Write-Host "1. Fermez ce terminal"
Write-Host "2. Ouvrez un nouveau terminal"
Write-Host "3. Lancez: .\GIT-INIT-PROJET.bat"

Read-Host "Appuyez sur Entree"
