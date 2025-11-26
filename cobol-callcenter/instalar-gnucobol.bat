@echo off
REM Script para baixar e instalar GnuCOBOL automaticamente

echo ==========================================
echo INSTALADOR GNUCOBOL - EATSFOOD
echo ==========================================
echo.

echo Baixando GnuCOBOL 3.2 (binarios compilados)...
echo.

REM Criar pasta temporaria
if not exist "%TEMP%\gnucobol" mkdir "%TEMP%\gnucobol"

REM Baixar usando PowerShell
powershell -Command "& {[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12; Invoke-WebRequest -Uri 'https://sourceforge.net/projects/gnucobol/files/gnucobol/3.2/GnuCOBOL_3.2_vs2019_x64.exe/download' -OutFile '%TEMP%\gnucobol\GnuCOBOL_installer.exe'}"

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo ERRO ao baixar o arquivo!
    echo Por favor, baixe manualmente de:
    echo https://sourceforge.net/projects/gnucobol/files/gnucobol/3.2/
    echo.
    echo Procure por: GnuCOBOL_3.2_vs2019_x64.exe
    echo.
    pause
    exit /b 1
)

echo.
echo Download concluido!
echo.
echo Iniciando instalador...
echo IMPORTANTE: Durante a instalacao, anote o caminho onde sera instalado!
echo.
pause

REM Executar instalador
start /wait %TEMP%\gnucobol\GnuCOBOL_installer.exe

echo.
echo ==========================================
echo Instalacao concluida!
echo ==========================================
echo.
echo Agora feche este terminal e abra um NOVO PowerShell
echo Depois execute: cobc --version
echo.
echo Se nao funcionar, execute este comando (como Admin):
echo.
echo [Environment]::SetEnvironmentVariable("Path", [Environment]::GetEnvironmentVariable("Path", "Machine") + ";C:\GnuCOBOL\bin", "Machine")
echo.
pause
