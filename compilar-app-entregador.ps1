# Script para compilar o APK do App Entregador
# Execute este script no PowerShell

Write-Host "==================================" -ForegroundColor Cyan
Write-Host "  Compilador App Entregador" -ForegroundColor Cyan
Write-Host "==================================" -ForegroundColor Cyan
Write-Host ""

# Verificar se estamos no diretório correto
if (!(Test-Path "app-entregador-android")) {
    Write-Host "ERRO: Diretorio app-entregador-android nao encontrado!" -ForegroundColor Red
    Write-Host "Execute este script na raiz do projeto." -ForegroundColor Yellow
    exit 1
}

Set-Location app-entregador-android

Write-Host "1. Limpando builds anteriores..." -ForegroundColor Yellow
if (Test-Path "gradlew.bat") {
    & .\gradlew.bat clean
} else {
    Write-Host "AVISO: gradlew.bat nao encontrado. Pulando limpeza." -ForegroundColor Yellow
}

Write-Host ""
Write-Host "2. Escolha o tipo de build:" -ForegroundColor Green
Write-Host "  [1] Debug   - Para testes (mais rapido)" -ForegroundColor White
Write-Host "  [2] Release - Para producao (requer assinatura)" -ForegroundColor White
Write-Host ""

$buildType = Read-Host "Digite 1 ou 2"

if ($buildType -eq "1") {
    Write-Host ""
    Write-Host "3. Compilando APK Debug..." -ForegroundColor Yellow
    & .\gradlew.bat assembleDebug
    
    $apkPath = "app\build\outputs\apk\debug\app-debug.apk"
    
    if (Test-Path $apkPath) {
        Write-Host ""
        Write-Host "==================================" -ForegroundColor Green
        Write-Host "  APK COMPILADO COM SUCESSO!" -ForegroundColor Green
        Write-Host "==================================" -ForegroundColor Green
        Write-Host ""
        Write-Host "Localização do APK:" -ForegroundColor Cyan
        Write-Host (Resolve-Path $apkPath) -ForegroundColor White
        Write-Host ""
        Write-Host "Para instalar no dispositivo:" -ForegroundColor Yellow
        Write-Host "  adb install $apkPath" -ForegroundColor White
        Write-Host ""
        
        # Perguntar se deseja instalar agora
        $install = Read-Host "Deseja instalar no dispositivo conectado agora? (S/N)"
        if ($install -eq "S" -or $install -eq "s") {
            Write-Host ""
            Write-Host "Instalando no dispositivo..." -ForegroundColor Yellow
            adb install -r $apkPath
        }
        
        # Perguntar se deseja abrir a pasta
        $open = Read-Host "Deseja abrir a pasta do APK? (S/N)"
        if ($open -eq "S" -or $open -eq "s") {
            Start-Process (Split-Path (Resolve-Path $apkPath))
        }
    } else {
        Write-Host ""
        Write-Host "ERRO: Falha ao compilar o APK!" -ForegroundColor Red
        Write-Host "Verifique os logs acima para detalhes." -ForegroundColor Yellow
    }
    
} elseif ($buildType -eq "2") {
    Write-Host ""
    Write-Host "3. Compilando APK Release..." -ForegroundColor Yellow
    Write-Host "ATENCAO: Certifique-se de ter configurado o keystore!" -ForegroundColor Red
    Write-Host ""
    
    & .\gradlew.bat assembleRelease
    
    $apkPath = "app\build\outputs\apk\release\app-release.apk"
    
    if (Test-Path $apkPath) {
        Write-Host ""
        Write-Host "==================================" -ForegroundColor Green
        Write-Host "  APK RELEASE COMPILADO!" -ForegroundColor Green
        Write-Host "==================================" -ForegroundColor Green
        Write-Host ""
        Write-Host "Localização do APK:" -ForegroundColor Cyan
        Write-Host (Resolve-Path $apkPath) -ForegroundColor White
        Write-Host ""
        
        # Abrir pasta automaticamente
        Start-Process (Split-Path (Resolve-Path $apkPath))
    } else {
        Write-Host ""
        Write-Host "ERRO: Falha ao compilar o APK Release!" -ForegroundColor Red
        Write-Host "Verifique se o keystore está configurado corretamente." -ForegroundColor Yellow
    }
    
} else {
    Write-Host ""
    Write-Host "Opção inválida! Execute o script novamente." -ForegroundColor Red
}

Set-Location ..

Write-Host ""
Write-Host "Processo finalizado." -ForegroundColor Cyan
Write-Host ""
