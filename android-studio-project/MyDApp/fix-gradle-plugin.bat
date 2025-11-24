@echo off
REM Script para resolver erro de plugin Gradle

setlocal enabledelayedexpansion
cd /d "%~dp0"

echo.
echo ========================================
echo   CORRIGINDO ERRO DO GRADLE
echo   Plugin nao encontrado
echo ========================================
echo.

echo [PASSO 1] Limpando cache...
if exist .gradle (
    echo   - Removendo .gradle...
    rmdir /s /q .gradle >nul 2>&1
    echo     OK
)

if exist app\build (
    echo   - Removendo app\build...
    rmdir /s /q app\build >nul 2>&1
    echo     OK
)

if exist .idea\caches (
    echo   - Removendo .idea\caches...
    rmdir /s /q .idea\caches >nul 2>&1
    echo     OK
)

if exist .idea\gradle.xml (
    del /q .idea\gradle.xml >nul 2>&1
)

echo.
echo [PASSO 2] Verificando arquivos...
if exist build.gradle (
    echo   [OK] build.gradle (raiz)
) else (
    echo   [ERRO] build.gradle (raiz) nao encontrado
)

if exist app\build.gradle (
    echo   [OK] app/build.gradle
) else (
    echo   [ERRO] app/build.gradle nao encontrado
)

if exist gradle.properties (
    echo   [OK] gradle.properties
) else (
    echo   [ERRO] gradle.properties nao encontrado
)

if exist gradle\wrapper\gradle-wrapper.properties (
    echo   [OK] gradle-wrapper.properties
) else (
    echo   [ERRO] gradle-wrapper.properties nao encontrado
)

echo.
echo ========================================
echo   PROXIMO PASSO:
echo ========================================
echo.
echo 1. Feche o Android Studio completamente
echo 2. Abra novamente o projeto
echo 3. Aguarde sincronizar
echo 4. Se pedir "Sync Now", clique
echo 5. Build ^> Clean Project
echo 6. Build ^> Rebuild Project
echo.
pause
