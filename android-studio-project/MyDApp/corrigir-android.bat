@echo off
REM Solução definitiva para erro "No main class defined" no Android Studio
REM Este script reconstrói a configuração do projeto Android

setlocal enabledelayedexpansion

title Corrigindo Projeto Android - MyD Bar & Restaurantes
color 0A
cls

echo.
echo ========================================
echo   CORRIGINDO ERRO DO ANDROID STUDIO
echo   Projeto: MyD Bar ^& Restaurantes
echo ========================================
echo.

REM Voltar para o diretório correto
cd /d "%~dp0"

echo [PASSO 1] Limpando cache e arquivos temporários...
echo.

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
    echo   - Removendo .idea\gradle.xml...
    del /q .idea\gradle.xml >nul 2>&1
    echo     OK
)

REM Recriar estrutura .idea
if not exist .idea mkdir .idea
if not exist .idea\modules mkdir .idea\modules
if not exist .idea\modules\app mkdir .idea\modules\app

echo.
echo [PASSO 2] Recriando configuração do IDE...
echo.

REM Criar .idea\vcs.xml
echo   - Criando vcs.xml...
(
    echo ^<?xml version="1.0" encoding="UTF-8"?^>
    echo ^<project version="4"^>
    echo     ^<component name="VcsDirectoryMappings"^>
    echo         ^<mapping directory="$PROJECT_DIR$" vcs="Git" /^>
    echo     ^</component^>
    echo ^</project^>
) > .idea\vcs.xml
echo     OK

REM Criar .idea\projectCodeStyle.xml
echo   - Criando projectCodeStyle.xml...
(
    echo ^<?xml version="1.0" encoding="UTF-8"?^>
    echo ^<project version="4"^>
    echo     ^<component name="ProjectCodeStyleConfiguration"^>
    echo         ^<state^>
    echo             ^<option name="USE_PER_PROJECT_SETTINGS" value="true" /^>
    echo         ^</state^>
    echo     ^</component^>
    echo ^</project^>
) > .idea\projectCodeStyle.xml
echo     OK

echo.
echo [PASSO 3] Verificando estrutura do projeto...
echo.

set "missing=0"

if exist app\src\main\java\com\myd\restaurante\MainActivity.kt (
    echo   [✓] MainActivity.kt
) else (
    echo   [✗] MainActivity.kt NAO ENCONTRADO
    set "missing=1"
)

if exist app\src\main\AndroidManifest.xml (
    echo   [✓] AndroidManifest.xml
) else (
    echo   [✗] AndroidManifest.xml NAO ENCONTRADO
    set "missing=1"
)

if exist app\build.gradle (
    echo   [✓] app/build.gradle
) else (
    echo   [✗] app/build.gradle NAO ENCONTRADO
    set "missing=1"
)

if exist app\src\main\res\layout\activity_main.xml (
    echo   [✓] activity_main.xml
) else (
    echo   [✗] activity_main.xml NAO ENCONTRADO
    set "missing=1"
)

echo.
if !missing! equ 0 (
    echo [SUCESSO] Todas as verificacoes passaram!
    echo.
    echo ========================================
    echo   PROXIMOS PASSOS:
    echo ========================================
    echo.
    echo 1. FECHE o Android Studio completamente
    echo    (não apenas minimize, feche totalmente)
    echo.
    echo 2. ABRA novamente o projeto
    echo    Caminho: c:\xampp\htdocs\myd_bar_restaurantes\android-studio-project\MyDApp
    echo.
    echo 3. AGUARDE a sincronização automática
    echo    (pode levar 2-5 minutos)
    echo.
    echo 4. Se pedir para sincronizar:
    echo    - Clique em "Sync Now"
    echo    - Ou vá em: File ^> Sync Project with Gradle Files
    echo.
    echo 5. Limpe o projeto:
    echo    - Build ^> Clean Project
    echo    - Build ^> Rebuild Project
    echo.
    echo 6. Execute:
    echo    - Shift+F10
    echo    - Ou: Run ^> Run 'app'
    echo    - Selecione um emulador ou dispositivo
    echo.
    echo ========================================
) else (
    echo [ERRO] Alguns arquivos estão faltando!
    echo Verifique a estrutura do projeto.
)

echo.
pause
