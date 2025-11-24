# Script para corrigir erro de Gradle no Android Studio

Write-Host "================================" -ForegroundColor Cyan
Write-Host "🔧 Corrigindo projeto Android" -ForegroundColor Cyan
Write-Host "================================" -ForegroundColor Cyan

# Diretório do projeto
$projectDir = Get-Location

Write-Host "`n1️⃣ Limpando cache e build..." -ForegroundColor Yellow

# Remover .gradle
if (Test-Path "$projectDir\.gradle") {
    Remove-Item "$projectDir\.gradle" -Recurse -Force
    Write-Host "   ✅ .gradle removido" -ForegroundColor Green
}

# Remover build
if (Test-Path "$projectDir\app\build") {
    Remove-Item "$projectDir\app\build" -Recurse -Force
    Write-Host "   ✅ build removido" -ForegroundColor Green
}

# Remover .idea
if (Test-Path "$projectDir\.idea") {
    Remove-Item "$projectDir\.idea" -Recurse -Force
    Write-Host "   ✅ .idea removido" -ForegroundColor Green
}

Write-Host "`n2️⃣ Sincronizando Gradle..." -ForegroundColor Yellow

# Executar gradle wrapper
if (Test-Path "$projectDir\gradlew.bat") {
    Write-Host "   Executando: .\gradlew.bat clean" -ForegroundColor Cyan
    & .\gradlew.bat clean
    
    Write-Host "   Executando: .\gradlew.bat sync" -ForegroundColor Cyan
    & .\gradlew.bat build -x test
} else {
    Write-Host "   ⚠️ gradlew.bat não encontrado" -ForegroundColor Red
    Write-Host "   Usando gradle do PATH..." -ForegroundColor Yellow
    gradle clean
    gradle build -x test
}

Write-Host "`n3️⃣ Verificando estrutura..." -ForegroundColor Yellow

# Verificar MainActivity
$mainActivity = "$projectDir\app\src\main\java\com\myd\restaurante\MainActivity.kt"
if (Test-Path $mainActivity) {
    Write-Host "   ✅ MainActivity.kt encontrado" -ForegroundColor Green
} else {
    Write-Host "   ❌ MainActivity.kt NÃO encontrado" -ForegroundColor Red
}

# Verificar AndroidManifest
$manifest = "$projectDir\app\src\main\AndroidManifest.xml"
if (Test-Path $manifest) {
    Write-Host "   ✅ AndroidManifest.xml encontrado" -ForegroundColor Green
} else {
    Write-Host "   ❌ AndroidManifest.xml NÃO encontrado" -ForegroundColor Red
}

# Verificar build.gradle
$buildGradle = "$projectDir\app\build.gradle"
if (Test-Path $buildGradle) {
    Write-Host "   ✅ app/build.gradle encontrado" -ForegroundColor Green
} else {
    Write-Host "   ❌ app/build.gradle NÃO encontrado" -ForegroundColor Red
}

Write-Host "`n================================" -ForegroundColor Cyan
Write-Host "✅ Processo completo!" -ForegroundColor Green
Write-Host "================================" -ForegroundColor Cyan
Write-Host "`n📝 Próximos passos:" -ForegroundColor Yellow
Write-Host "   1. Feche o Android Studio completamente" -ForegroundColor White
Write-Host "   2. Abra o projeto novamente" -ForegroundColor White
Write-Host "   3. Aguarde o Gradle sincronizar (File > Sync Now)" -ForegroundColor White
Write-Host "   4. Tente rodar novamente (Shift + F10 ou Run > Run 'app')" -ForegroundColor White
Write-Host "`n"
