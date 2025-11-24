# Build App Entregador para Android
Write-Host "🚀 Build App Entregador para Android" -ForegroundColor Cyan

# Caminhos
$sourceDir = "public/app-entregador"
$androidDir = "android-entregador/app/src/main/assets"

# Verifica se o diretório Android existe
if (-not (Test-Path $androidDir)) {
    Write-Host "❌ Diretório Android não encontrado: $androidDir" -ForegroundColor Red
    Write-Host "   Certifique-se de ter criado o projeto Android primeiro" -ForegroundColor Yellow
    exit 1
}

Write-Host "📦 Copiando arquivos..." -ForegroundColor Yellow

# Limpa assets antigos
if (Test-Path "$androidDir/*") {
    Remove-Item "$androidDir/*" -Recurse -Force
}

# Copia todos os arquivos
Copy-Item -Path "$sourceDir/*" -Destination $androidDir -Recurse -Force

Write-Host "✅ Arquivos copiados para: $androidDir" -ForegroundColor Green

# Lista arquivos copiados
Write-Host "`n📋 Arquivos no assets:" -ForegroundColor Cyan
Get-ChildItem -Path $androidDir -Recurse | Select-Object FullName

Write-Host "`n✅ Build concluído!" -ForegroundColor Green
Write-Host "   Agora você pode compilar o APK no Android Studio" -ForegroundColor Yellow
