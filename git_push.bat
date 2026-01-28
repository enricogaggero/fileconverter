@echo off
chcp 65001 >nul
setlocal EnableDelayedExpansion

echo ============================================
echo    fileconverter - Git Push/Update Script
echo ============================================
echo.

:: Verifica se siamo in una repo git
if not exist ".git" (
    echo [INFO] Repository Git non trovata. Inizializzazione...
    echo.

    set /p REPO_URL="Inserisci URL repository GitHub (es: https://github.com/enricogaggero/leadhub.git): "

    if "!REPO_URL!"=="" (
        echo [ERRORE] URL repository non fornito. Uscita.
        pause
        exit /b 1
    )

    git init
    git remote add origin !REPO_URL!
    echo [OK] Repository inizializzata con remote: !REPO_URL!
    echo.
)

:: Mostra stato corrente
echo [INFO] Stato corrente del repository:
echo ----------------------------------------
git status --short
echo ----------------------------------------
echo.

:: Chiedi conferma
set /p CONFIRM="Vuoi procedere con il commit e push? (S/N): "
if /i not "!CONFIRM!"=="S" (
    echo [INFO] Operazione annullata.
    pause
    exit /b 0
)

:: Chiedi messaggio commit
echo.
set /p COMMIT_MSG="Messaggio commit (default: 'Aggiornamento'): "
if "!COMMIT_MSG!"=="" set COMMIT_MSG=Aggiornamento

:: Aggiungi tutti i file
echo.
echo [INFO] Aggiunta file...
git add .

:: Mostra cosa verra' committato
echo.
echo [INFO] File che verranno committati:
echo ----------------------------------------
git status --short
echo ----------------------------------------

:: Commit
echo.
echo [INFO] Creazione commit...
git commit -m "!COMMIT_MSG!"

if errorlevel 1 (
    echo [WARN] Nessuna modifica da committare o errore nel commit.
) else (
    echo [OK] Commit creato con successo.
)

:: Verifica branch corrente
for /f "tokens=*" %%a in ('git branch --show-current 2^>nul') do set BRANCH=%%a
if "!BRANCH!"=="" set BRANCH=main

echo.
echo [INFO] Branch corrente: !BRANCH!

:: Push
echo.
echo [INFO] Push verso origin/!BRANCH!...
git push -u origin !BRANCH!

if errorlevel 1 (
    echo.
    echo [WARN] Push fallito. Potrebbe essere necessario:
    echo   1. Configurare le credenziali Git
    echo   2. Creare prima il repository su GitHub
    echo   3. Eseguire: git push --set-upstream origin !BRANCH!
    echo.
    set /p FORCE="Vuoi provare con force push? (S/N): "
    if /i "!FORCE!"=="S" (
        git push -u origin !BRANCH! --force
    )
) else (
    echo.
    echo [OK] Push completato con successo!
)

echo.
echo ============================================
echo    Operazione completata
echo ============================================
pause
