#!/usr/bin/env powershell
# Script para compilar o Android App com Firebase

$projectPath = "C:\xampp\htdocs\myd_bar_restaurantes\android-studio-project\MyDApp"

Write-Host "📱 Compilando Android App..." -ForegroundColor Green
Write-Host "Projeto: $projectPath" -ForegroundColor Cyan

# Verificar se ANDROID_HOME está definido
if (-not $env:ANDROID_HOME) {
    Write-Host "⚠️  ANDROID_HOME não definido. Procurando..." -ForegroundColor Yellow
    
    # Procurar em locais comuns
    $possiblePaths = @(
        "C:\Android\sdk",
        "C:\Program Files\Android\sdk",
        "$env:USERPROFILE\AppData\Local\Android\sdk"
    )
    
    foreach ($path in $possiblePaths) {
        if (Test-Path $path) {
            $env:ANDROID_HOME = $path
            Write-Host "✅ ANDROID_HOME encontrado: $path" -ForegroundColor Green
            break
        }
    }
}

if (-not $env:ANDROID_HOME) {
    Write-Host "❌ ANDROID_HOME não encontrado!" -ForegroundColor Red
    Write-Host "Configure ANDROID_HOME manualmente ou instale Android Studio" -ForegroundColor Red
    exit 1
}

# Verificar se há Java instalado
$javaPath = Get-Command java -ErrorAction SilentlyContinue
if (-not $javaPath) {
    Write-Host "❌ Java não encontrado!" -ForegroundColor Red
    exit 1
}

Write-Host "✅ Java encontrado: $($javaPath.Source)" -ForegroundColor Green

# Tentar compilar manualmente com os scripts existentes
if (Test-Path "$projectPath\fix-gradle.bat") {
    Write-Host "🔧 Executando fix-gradle.bat..." -ForegroundColor Cyan
    & cmd /c "$projectPath\fix-gradle.bat"
}

# Abrir instrucoes
Write-Host "`n📋 INSTRUÇÕES:" -ForegroundColor Yellow
Write-Host "1. Abra Android Studio" -ForegroundColor White
Write-Host "2. File → Open → selecione: C:\xampp\htdocs\myd_bar_restaurantes\android-studio-project\MyDApp" -ForegroundColor White
Write-Host "3. Espere indexar (2-3 min)" -ForegroundColor White
Write-Host "4. Build → Build APK(s)" -ForegroundColor White
Write-Host "5. O APK será gerado em: app\build\outputs\apk\debug\" -ForegroundColor White
Write-Host "`n✨ Para instalar no tablet:" -ForegroundColor Yellow
Write-Host "   adb install -r app/build/outputs/apk/debug/app-debug.apk" -ForegroundColor Cyan
