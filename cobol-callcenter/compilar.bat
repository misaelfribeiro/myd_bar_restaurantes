@echo off
REM Script para compilar e executar o sistema COBOL

echo ==========================================
echo COMPILANDO SISTEMA CALL CENTER - EATSFOOD
echo ==========================================
echo.

REM Verificar se o compilador COBOL esta instalado
where cobc >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo ERRO: Compilador COBOL nao encontrado!
    echo.
    echo Por favor, instale o GnuCOBOL:
    echo https://sourceforge.net/projects/gnucobol/
    echo.
    pause
    exit /b 1
)

echo Compilando CALLCENTER.cbl...
cobc -x -free CALLCENTER.cbl -o callcenter.exe

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ==========================================
    echo COMPILACAO CONCLUIDA COM SUCESSO!
    echo ==========================================
    echo.
    echo Iniciando sistema...
    echo.
    callcenter.exe
) else (
    echo.
    echo ERRO NA COMPILACAO!
    echo Verifique o codigo fonte e tente novamente.
    pause
)
