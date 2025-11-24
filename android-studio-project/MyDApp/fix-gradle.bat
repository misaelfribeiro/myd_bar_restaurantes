@echo off
chcp 65001 >nul
cls

echo.
echo ================================
echo Corrigindo projeto Android
echo ================================
echo.

REM Limpar cache
echo [1] Limpando cache e arquivos build...
if exist .gradle (
    rmdir /s /q .gradle
    echo OK - .gradle removido
)

if exist app\build (
    rmdir /s /q app\build
    echo OK - build removido
)

if exist .idea (
    rmdir /s /q .idea
    echo OK - .idea removido
)

echo.
echo [2] Executando Gradle clean...
call gradlew.bat clean

echo.
echo [3] Verificando arquivos importantes...

if exist app\src\main\java\com\myd\restaurante\MainActivity.kt (
    echo OK - MainActivity.kt encontrado
) else (
    echo ERRO - MainActivity.kt nao encontrado
)

if exist app\src\main\AndroidManifest.xml (
    echo OK - AndroidManifest.xml encontrado
) else (
    echo ERRO - AndroidManifest.xml nao encontrado
)

if exist app\build.gradle (
    echo OK - app/build.gradle encontrado
) else (
    echo ERRO - app/build.gradle nao encontrado
)

echo.
echo ================================
echo Processo completo!
echo ================================
echo.
echo Proximos passos:
echo 1. Feche o Android Studio
echo 2. Abra o projeto novamente
echo 3. Aguarde sincronizar (File ^> Sync Now)
echo 4. Rode novamente (Shift+F10)
echo.
pause
