@echo off
REM Script para construir APK com Cordova
REM Requisitos: Node.js, Android SDK, Java 8+

SETLOCAL ENABLEDELAYEDEXPANSION

echo.
echo ======================================
echo  MyD Bar - Construtor de APK
echo ======================================
echo.

REM Verificar Node.js
where node >nul 2>nul
IF %ERRORLEVEL% NEQ 0 (
    echo ❌ Node.js não está instalado ou não está no PATH
    echo Baixe em: https://nodejs.org/
    pause
    exit /b 1
)
echo ✅ Node.js encontrado

REM Verificar Android SDK
IF NOT EXIST "%LOCALAPPDATA%\Android\sdk" (
    echo ❌ Android SDK não encontrado
    echo Configure ANDROID_HOME ou instale Android Studio
    pause
    exit /b 1
)
echo ✅ Android SDK encontrado

REM Configurar variáveis
SET ANDROID_HOME=%LOCALAPPDATA%\Android\sdk
SET PATH=%ANDROID_HOME%\tools;%ANDROID_HOME%\tools\bin;%ANDROID_HOME%\platform-tools;%PATH%

REM Tentar construir
echo.
echo Construindo APK...
echo.

cd /d "%~dp0cordova-app\MyDApp"

REM Modo debug (menor footprint)
call cordova build android

IF %ERRORLEVEL% EQU 0 (
    echo.
    echo ✅ APK construída com sucesso!
    echo.
    echo 📁 Localização da APK:
    echo %cd%\platforms\android\app\build\outputs\apk\debug\app-debug.apk
    echo.
    echo 📱 Para instalar:
    echo 1. Copie o arquivo para seu celular
    echo 2. Abra e confirme a instalação
    echo.
    pause
) ELSE (
    echo.
    echo ❌ Erro na construção
    echo Verifique as mensagens acima
    pause
    exit /b 1
)
