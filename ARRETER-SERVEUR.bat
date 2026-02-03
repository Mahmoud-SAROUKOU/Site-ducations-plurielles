@echo off
title Arret Serveur PHP
echo Arret du serveur PHP...
for /f "tokens=5" %%a in ('netstat -ano ^| findstr ":8000"') do (
    taskkill /F /PID %%a >nul 2>nul
)
echo Serveur arrete.
timeout /t 2 >nul
