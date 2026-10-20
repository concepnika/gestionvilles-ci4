@echo off
REM ============================================================
REM  Setup local GestionVilles CI3 + CI4 (Surface Pro 7)
REM  A executer EN TANT QU'ADMINISTRATEUR :
REM  clic droit > "Executer en tant qu'administrateur"
REM
REM  - Ajoute gestionvilles.local (CI3) et gestionvillesci4.local (CI4)
REM    au fichier hosts
REM  - Demarre les services Wamp (MySQL puis Apache)
REM
REM  Les vhosts Apache sont deja configures dans
REM  C:\wamp64\bin\apache\apache2.4.65\conf\extra\httpd-vhosts.conf
REM ============================================================

REM --- 1. Entrees hosts ---
findstr /C:"gestionvilles.local" "%WINDIR%\System32\drivers\etc\hosts" >nul 2>&1
if errorlevel 1 (
    echo 127.0.0.1 gestionvilles.local>> "%WINDIR%\System32\drivers\etc\hosts"
    echo ::1 gestionvilles.local>> "%WINDIR%\System32\drivers\etc\hosts"
    echo [OK] gestionvilles.local ajoute au fichier hosts
) else (
    echo [SKIP] gestionvilles.local deja present
)

findstr /C:"gestionvillesci4.local" "%WINDIR%\System32\drivers\etc\hosts" >nul 2>&1
if errorlevel 1 (
    echo 127.0.0.1 gestionvillesci4.local>> "%WINDIR%\System32\drivers\etc\hosts"
    echo ::1 gestionvillesci4.local>> "%WINDIR%\System32\drivers\etc\hosts"
    echo [OK] gestionvillesci4.local ajoute au fichier hosts
) else (
    echo [SKIP] gestionvillesci4.local deja present
)

REM --- 2. (Re)demarrage des services Wamp ---
REM Apache est REDEMARRE pour recharger httpd-vhosts.conf
REM (idempotent : peut etre relance apres toute modif de config).
echo.
echo Redemarrage de MySQL...
net stop wampmysqld64 >nul 2>&1
net start wampmysqld64

echo Redemarrage d'Apache (recharge la config vhosts)...
net stop wampapache64 >nul 2>&1
net start wampapache64

echo.
echo ============================================================
echo  Termine. Testez ensuite :
echo    http://gestionvillesci4.local/      (statut CI4)
echo    http://gestionvilles.local/         (CI3 legacy)
echo ============================================================
pause
